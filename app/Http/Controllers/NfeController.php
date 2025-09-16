<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Nfe;
use App\Models\NfeItem;
use App\Models\NfeDuplicata;
use App\Services\FocusNfeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NfeController extends Controller
{
    protected $focusNfeService;

    public function __construct(FocusNfeService $focusNfeService)
    {
        $this->middleware('auth');
        $this->focusNfeService = $focusNfeService;
    }
    /**
     * Cancelar NFe (somente até 24h após emissão)
     */
    public function cancelarNfe24h(Nfe $nfe)
    {
        try {
            \Log::info('Iniciando cancelamento NFe 24h', [
                'nfe_id' => $nfe->id,
                'status' => $nfe->status,
                'ref' => $nfe->ref,
                'data_emissao' => $nfe->data_emissao
            ]);

            // Verificar se a NFe pode ser cancelada
            $statusPermitidos = ['emitida', 'autorizado', 'processando_autorizacao'];
            if (!in_array($nfe->status, $statusPermitidos)) {
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'Só é possível cancelar NFes emitidas ou autorizadas. Status atual: ' . $nfe->status);
            }

            // Se estiver processando, simular cancelamento direto
            if ($nfe->status === 'processando_autorizacao') {
                $nfe->status = 'cancelado';
                $nfe->save();
                
                \Log::info('NFe cancelada (estava processando)', ['nfe_id' => $nfe->id]);
                
                return redirect()->route('nfe.show', $nfe)
                    ->with('success', 'NFe cancelada com sucesso! (cancelamento direto pois estava em processamento)');
            }

            // Verificar se está dentro do prazo de 24h
            if (!$nfe->data_emissao) {
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'Data de emissão não encontrada na NFe.');
            }

            $limite = $nfe->data_emissao->addHours(24);
            if (now()->greaterThan($limite)) {
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'Só é possível cancelar NFes em até 24h após autorização. Limite: ' . $limite->format('d/m/Y H:i'));
            }

            // Verificar se tem referência para API
            if (!$nfe->ref) {
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'NFe não possui referência para cancelamento via API.');
            }

            // Tentar cancelar via API Focus NFe
            $resultado = $this->focusNfeService->cancelarNfe($nfe->ref, 'Cancelamento dentro de 24h');
            
            \Log::info('Resultado cancelamento NFe 24h', [
                'nfe_id' => $nfe->id,
                'ref' => $nfe->ref,
                'resultado' => $resultado
            ]);

            // Verificar se cancelamento foi bem-sucedido
            $cancelamentoOK = ($resultado['sucesso'] ?? false) || 
                            ($resultado['status'] ?? '') === 'cancelado' ||
                            (isset($resultado['status_sefaz']) && $resultado['status_sefaz'] === '135');

            if ($cancelamentoOK) {
                $nfe->status = 'cancelado';
                $nfe->save();
                
                \Log::info('NFe cancelada via 24h - status atualizado', [
                    'nfe_id' => $nfe->id,
                    'novo_status' => 'cancelado'
                ]);
                
                return redirect()->route('nfe.show', $nfe)
                    ->with('success', 'NFe cancelada com sucesso!');
            } else {
                $mensagemErro = $resultado['mensagem'] ?? $resultado['erro'] ?? $resultado['mensagem_sefaz'] ?? 'Erro desconhecido ao cancelar NFe';
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'Erro ao cancelar NFe: ' . $mensagemErro);
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao cancelar NFe 24h', [
                'nfe_id' => $nfe->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Erro interno ao cancelar NFe: ' . $e->getMessage());
        }
    }

    /**
     * Devolver NFe
     */
    public function devolverNfe(Nfe $nfe, Request $request)
    {
        try {
            \Log::info('Iniciando devolução NFe', [
                'nfe_id' => $nfe->id,
                'status' => $nfe->status,
                'chave_nfe' => $nfe->chave_nfe
            ]);

            // Verificar se a NFe pode ser devolvida
            $statusPermitidos = ['emitida', 'autorizado', 'processando_autorizacao'];
            if (!in_array($nfe->status, $statusPermitidos)) {
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'Só é possível devolver NFes emitidas ou autorizadas. Status atual: ' . $nfe->status);
            }

            // Verificar se a NFe já foi devolvida
            if ($nfe->status === 'devolvida') {
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'Esta NFe já foi devolvida.');
            }

            // Se estiver processando, simular devolução direto
            if ($nfe->status === 'processando_autorizacao') {
                $motivo = $request->input('motivo', 'Devolução comercial');
                $justificativa = $request->input('justificativa', 'Devolução solicitada pelo cliente');
                
                $nfe->status = 'devolvida';
                $nfe->data_devolucao = now();
                $nfe->justificativa_devolucao = $motivo . ' - ' . $justificativa;
                $nfe->save();
                
                \Log::info('NFe devolvida (estava processando)', [
                    'nfe_id' => $nfe->id,
                    'motivo' => $motivo,
                    'justificativa' => $justificativa
                ]);
                
                return redirect()->route('nfe.show', $nfe)
                    ->with('success', 'NFe devolvida com sucesso! Motivo: ' . $motivo);
            }

            // Verificar dados necessários
            if (!$nfe->chave_nfe) {
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'NFe não possui chave para processamento da devolução.');
            }

            // Dados para a devolução via API SEFAZ
            $dadosDevolucao = [
                'chave' => $nfe->chave_nfe,
                'numero' => $nfe->numero_nfe,
                'serie' => $nfe->serie_nfe,
                'motivo' => $request->input('motivo', 'Devolução comercial'),
                'justificativa' => $request->input('justificativa', 'Devolução solicitada pelo cliente'),
                'data_devolucao' => now()->format('Y-m-d H:i:s')
            ];

            \Log::info('Iniciando devolução NFe', [
                'nfe_id' => $nfe->id,
                'dados' => $dadosDevolucao
            ]);

            // Simulação da integração com API SEFAZ para devolução
            // Em produção, aqui seria feita a chamada real para a API da SEFAZ
            $resultadoAPI = $this->processarDevolucaoSEFAZ($dadosDevolucao);

            \Log::info('Resultado processamento devolução', [
                'nfe_id' => $nfe->id,
                'resultado' => $resultadoAPI
            ]);

            if ($resultadoAPI['sucesso']) {
                // Atualizar status da NFe
                $nfe->status = 'devolvida';
                $nfe->data_devolucao = now();
                $nfe->protocolo_devolucao = $resultadoAPI['protocolo'] ?? null;
                $nfe->justificativa_devolucao = $dadosDevolucao['motivo'] . ' - ' . $dadosDevolucao['justificativa'];
                $nfe->status_devolucao = 'processada';
                $nfe->mensagem_devolucao_sefaz = $resultadoAPI['mensagem'] ?? 'Devolução processada com sucesso';
                $nfe->save();

                // Log da operação
                \Log::info("NFe {$nfe->numero_nfe} devolvida com sucesso", [
                    'nfe_id' => $nfe->id,
                    'chave' => $nfe->chave_nfe,
                    'protocolo' => $resultadoAPI['protocolo'],
                    'user_id' => Auth::id()
                ]);

                return redirect()->route('nfe.show', $nfe)
                    ->with('success', 'NFe devolvida com sucesso! Protocolo: ' . ($resultadoAPI['protocolo'] ?? 'N/A'));
            } else {
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'Erro ao devolver NFe: ' . ($resultadoAPI['erro'] ?? 'Erro desconhecido'));
            }

        } catch (\Exception $e) {
            \Log::error("Erro ao devolver NFe {$nfe->id}: " . $e->getMessage(), [
                'nfe_id' => $nfe->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Erro interno ao processar devolução: ' . $e->getMessage());
        }
    }

    /**
     * Processar devolução na SEFAZ (simulação)
     */
    private function processarDevolucaoSEFAZ($dados)
    {
        // Simulação de processamento SEFAZ
        // Em produção, aqui seria feita a integração real com a API da SEFAZ
        
        try {
            // Limpar a chave NFe (remover prefixo "NFe" se existir)
            $chave = $dados['chave'];
            if (strpos($chave, 'NFe') === 0) {
                $chave = substr($chave, 3); // Remove "NFe" do início
            }
            
            // Validações básicas
            if (empty($chave) || strlen($chave) !== 44) {
                \Log::error('Chave NFe inválida para devolução', [
                    'chave_original' => $dados['chave'],
                    'chave_limpa' => $chave,
                    'tamanho' => strlen($chave)
                ]);
                return [
                    'sucesso' => false,
                    'erro' => 'Chave da NFe inválida - esperado 44 caracteres, recebido: ' . strlen($chave)
                ];
            }

            // Simular chamada à API SEFAZ
            sleep(1); // Simular tempo de processamento

            // Gerar protocolo fictício
            $protocolo = 'DEV' . date('Ymd') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);

            return [
                'sucesso' => true,
                'protocolo' => $protocolo,
                'data_processamento' => now()->format('Y-m-d H:i:s'),
                'status_sefaz' => 'Devolução autorizada',
                'mensagem' => 'Devolução processada com sucesso'
            ];

        } catch (\Exception $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro na comunicação com SEFAZ: ' . $e->getMessage()
            ];
        }
    }
// ...existing code...

    /**
     * Mostrar painel de emissão de NFe
     */
    public function painel()
    {
        // Buscar produtos da empresa
        $produtos = Product::where('company_id', Auth::user()->company_id ?? 1)
                          ->select('id', 'name', 'sale_price', 'codigo', 'ncm', 'internal_code', 'unit')
                          ->orderBy('name')
                          ->get();
        
        return view('nfe.painel', compact('produtos'));
    }

    /**
     * Listar NFes
     */
    public function index(Request $request)
    {
        $query = Nfe::with(['company'])
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('numero')) {
            $query->where('numero', 'like', '%' . $request->numero . '%');
        }

        if ($request->filled('cliente')) {
            $query->where('destinatario_nome', 'like', '%' . $request->cliente . '%');
        }

        $nfes = $query->paginate(20);

        return view('nfe.index', compact('nfes'));
    }

    /**
     * Mostrar formulário de criação
     */
    public function create()
    {
        return view('nfe.create');
    }

    /**
     * Salvar nova NFe
     */
    public function store(Request $request)
    {
        \Log::info('NFe Store chamado', ['action' => $request->input('action'), 'data' => $request->all()]);
        
        $request->validate([
            'natureza_operacao' => 'required|string|max:60',
            'data_emissao' => 'required|date',
            
            // Destinatário
            'destinatario_nome' => 'required|string|max:60',
            'logradouro_destinatario' => 'required|string',
            'numero_destinatario' => 'required|string',
            'bairro_destinatario' => 'required|string',
            'municipio_destinatario' => 'required|string',
            'uf_destinatario' => 'required|string|size:2',
            'cep_destinatario' => 'required|string',
            
            // Itens
            'itens' => 'required|array|min:1',
            'itens.*.descricao' => 'required|string',
            'itens.*.ncm' => 'required|string',
            'itens.*.unidade' => 'required|string',
            'itens.*.quantidade' => 'required|numeric|min:0.01',
            'itens.*.valor_unitario' => 'required|numeric|min:0.01',
        ]);

        // Validação customizada: CNPJ OU CPF deve estar preenchido
        if (empty($request->destinatario_cnpj_cpf)) {
            return back()->withErrors([
                'destinatario_cnpj_cpf' => 'É obrigatório informar CNPJ ou CPF do destinatário.'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            // Criar NFe
            $nfe = new Nfe();
            $nfe->company_id = Auth::user()->company_id;
            $nfe->ref = 'NFE_' . time() . '_' . Auth::user()->company_id;
            $nfe->status = $request->input('action') === 'emit' ? 'processando_autorizacao' : 'rascunho';
            
            // Informações gerais
            $nfe->natureza_operacao = $request->natureza_operacao;
            $nfe->tipo_documento = $request->tipo_documento ?? '1'; // 1 = NFe
            $nfe->finalidade_emissao = $request->finalidade_emissao ?? '1'; // 1 = Normal
            $nfe->consumidor_final = $request->consumidor_final ?? '1'; // 1 = Consumidor final
            $nfe->presenca_comprador = $request->presenca_comprador ?? '1'; // 1 = Presencial
            
            // CORREÇÃO: Definir local_destino baseado no UF do destinatário  
            $company = Auth::user()->company;
            $ufEmitente = $company->uf ?? 'PA';
            $ufDestinatario = $request->uf_destinatario;
            
            \Log::info('Comparando UFs', [
                'uf_emitente' => $ufEmitente,
                'uf_destinatario' => $ufDestinatario,
                'mesma_uf' => $ufEmitente === $ufDestinatario
            ]);
            
            $nfe->local_destino = ($ufEmitente === $ufDestinatario) ? '1' : '2'; // 1 = Interna, 2 = Interestadual
            
            $nfe->modalidade_frete = $request->modalidade_frete ?? '9'; // 9 = Sem frete
            $nfe->data_emissao = $request->data_emissao;
            
            // Dados do emitente (pegar da empresa)
            $company = Auth::user()->company;
            $nfe->cnpj_emitente = preg_replace('/\D/', '', $company->cnpj ?? '00000000000000');
            $nfe->nome_emitente = $company->razao_social ?? 'Empresa Não Configurada';
            $nfe->ie_emitente = $company->inscricao_estadual ?? 'ISENTO';
            $nfe->logradouro_emitente = $company->endereco ?? 'N/A';
            $nfe->numero_emitente = $company->numero ?? 'S/N';
            $nfe->bairro_emitente = $company->bairro ?? 'N/A';
            $nfe->municipio_emitente = $company->cidade ?? 'N/A';
            $nfe->uf_emitente = $company->uf ?? 'SP';
            $nfe->cep_emitente = preg_replace('/\D/', '', $company->cep ?? '00000000');
            $nfe->regime_tributario_emitente = '1'; // Simples Nacional por padrão
            
            // Dados do destinatário (aceitar nomes alternativos)
            $nfe->nome_destinatario = $request->destinatario_nome;
            
            // CPF/CNPJ - pode vir em diferentes campos
            $cpfCnpj = $request->destinatario_cnpj_cpf;
            if ($cpfCnpj) {
                $cpfCnpj = preg_replace('/\D/', '', $cpfCnpj);
                if (strlen($cpfCnpj) == 11) {
                    $nfe->cpf_destinatario = $cpfCnpj;
                    $nfe->indicador_ie_destinatario = '9'; // Não contribuinte
                } else {
                    $nfe->cnpj_destinatario = $cpfCnpj;
                    $nfe->ie_destinatario = $request->ie_destinatario;
                    $nfe->indicador_ie_destinatario = $request->ie_destinatario ? '1' : '9';
                }
            } else {
                $nfe->indicador_ie_destinatario = '9'; // Padrão: Não contribuinte
            }
            
            $nfe->email_destinatario = $request->destinatario_email;
            $nfe->telefone_destinatario = $request->destinatario_telefone;
            $nfe->logradouro_destinatario = $request->logradouro_destinatario;
            $nfe->numero_destinatario = $request->numero_destinatario;
            $nfe->bairro_destinatario = $request->bairro_destinatario;
            $nfe->municipio_destinatario = $request->municipio_destinatario;
            $nfe->uf_destinatario = $request->uf_destinatario;
            $nfe->cep_destinatario = preg_replace('/\D/', '', $request->cep_destinatario);
            
            // Valores
            $nfe->valor_frete = $request->valor_frete ?? 0;
            $nfe->valor_seguro = $request->valor_seguro ?? 0;
            $nfe->valor_desconto = $request->valor_desconto ?? 0;
            $nfe->valor_outras_despesas = $request->valor_outras_despesas ?? 0;
            $nfe->valor_total = 0; // Será recalculado após adicionar os itens
            
            $nfe->save();
            
            // Criar itens
            $valorProdutos = 0;
            foreach ($request->itens as $index => $itemData) {
                $item = new NfeItem();
                $item->nfe_id = $nfe->id;
                $item->numero_item = $index + 1;
                $item->codigo_produto = $itemData['codigo'] ?? '';
                $item->descricao = $itemData['descricao'];
                $item->codigo_ncm = preg_replace('/\D/', '', $itemData['ncm'] ?? '49111090');
                
                // CORREÇÃO: Definir CFOP automaticamente se não informado ou vazio
                if (empty($itemData['cfop'])) {
                    // Determinar CFOP baseado na UF do destinatário vs emitente
                    $ufEmitente = $company->uf ?? 'PA';
                    $ufDestinatario = $request->uf_destinatario;
                    
                    if ($ufEmitente === $ufDestinatario) {
                        // Operação interna (mesma UF) - CFOP 5xxx
                        $item->cfop = '5102'; // Venda de mercadoria
                    } else {
                        // Operação interestadual (UF diferente) - CFOP 6xxx  
                        $item->cfop = '6102'; // Venda de mercadoria para outro estado
                    }
                    
                    \Log::info("CFOP definido automaticamente", [
                        'uf_emitente' => $ufEmitente,
                        'uf_destinatario' => $ufDestinatario,
                        'cfop_definido' => $item->cfop
                    ]);
                } else {
                    $item->cfop = $itemData['cfop'];
                }
                
                $item->unidade_comercial = $itemData['unidade'];
                $item->quantidade_comercial = floatval($itemData['quantidade']);
                $item->valor_unitario_comercial = floatval($itemData['valor_unitario']);
                $item->valor_bruto_produtos = $item->quantidade_comercial * $item->valor_unitario_comercial;
                
                // Impostos
                $item->icms_origem = $itemData['icms_origem'] ?? 0;
                $item->icms_situacao_tributaria = $itemData['icms_cst'] ?? '40';
                $item->pis_situacao_tributaria = $itemData['pis_cst'] ?? '01';
                $item->cofins_situacao_tributaria = $itemData['cofins_cst'] ?? '01';
                
                // Calcular impostos (valores básicos para exemplo)
                $item->icms_base_calculo = 0;
                $item->icms_aliquota = floatval($itemData['icms_aliquota'] ?? 0);
                $item->icms_valor = 0;
                
                $item->ipi_situacao_tributaria = '53'; // Não tributado
                $item->ipi_base_calculo = 0;
                $item->ipi_aliquota = 0;
                $item->ipi_valor = 0;
                
                $item->pis_base_calculo = $item->valor_bruto_produtos;
                $item->pis_aliquota = floatval($itemData['pis_aliquota'] ?? 0.65);
                $item->pis_valor = $item->valor_bruto_produtos * ($item->pis_aliquota / 100);
                
                $item->cofins_base_calculo = $item->valor_bruto_produtos;
                $item->cofins_aliquota = floatval($itemData['cofins_aliquota'] ?? 3.00);
                $item->cofins_valor = $item->valor_bruto_produtos * ($item->cofins_aliquota / 100);
                
                $item->valor_desconto = 0;
                $item->valor_frete = 0;
                $item->valor_seguro = 0;
                $item->valor_outras_despesas = 0;
                $item->valor_total_item = $item->valor_bruto_produtos;
                
                $item->save();
                
                $valorProdutos += $item->valor_bruto_produtos;
            }

            // Recalcular totais da NFe
            $nfe->valor_produtos = $valorProdutos;
            $nfe->valor_pis = $nfe->items()->sum('pis_valor');
            $nfe->valor_cofins = $nfe->items()->sum('cofins_valor');
            $nfe->valor_icms = $nfe->items()->sum('icms_valor');
            $nfe->valor_total = $valorProdutos + $nfe->valor_frete + $nfe->valor_seguro + $nfe->valor_outras_despesas - $nfe->valor_desconto;
            $nfe->save();

            // Processar duplicatas se existirem
            if ($request->has('duplicatas') && is_array($request->duplicatas)) {
                foreach ($request->duplicatas as $duplicataData) {
                    if (!empty($duplicataData['numero']) && !empty($duplicataData['data_vencimento']) && !empty($duplicataData['valor'])) {
                        $duplicata = new NfeDuplicata();
                        $duplicata->nfe_id = $nfe->id;
                        $duplicata->numero = $duplicataData['numero'];
                        $duplicata->data_vencimento = $duplicataData['data_vencimento'];
                        $duplicata->valor = floatval($duplicataData['valor']);
                        $duplicata->save();
                    }
                }
            }
            
            DB::commit();
            
            \Log::info('NFe salva com sucesso, verificando ação', ['action' => $request->input('action'), 'nfe_id' => $nfe->id]);
            
            // Se for para emitir, chamar API
            if ($request->input('action') === 'emit') {
                \Log::info('Chamando método emitir', ['nfe_id' => $nfe->id]);
                return $this->emitir($request, $nfe);
            }
            
            return redirect()->route('nfe.show', $nfe)
                ->with('success', 'NFe criada com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Erro no store da NFe', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return back()->withInput()
                ->with('error', 'Erro ao criar NFe: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar NFe específica
     */
    public function show(Nfe $nfe)
    {
        $nfe->load(['items', 'company']);
        return view('nfe.show', compact('nfe'));
    }

    /**
     * Mostrar formulário de edição
     */
    public function edit(Nfe $nfe)
    {
        if (!in_array($nfe->status, ['rascunho', 'erro'])) {
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Apenas NFes em rascunho ou com erro podem ser editadas.');
        }

        $nfe->load('items');
        
        // Se está editando uma NFe com erro, mudar status para rascunho
        if ($nfe->status === 'erro') {
            $nfe->status = 'rascunho';
            $nfe->mensagem_erro = null;
            $nfe->save();
        }
        
        return view('nfe.edit_full', compact('nfe'));
    }

    /**
     * Atualizar NFe
     */
    public function update(Request $request, Nfe $nfe)
    {
        if (!in_array($nfe->status, ['rascunho', 'erro'])) {
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Apenas NFes em rascunho ou com erro podem ser editadas.');
        }

        // Validação básica dos campos da nova view
        $request->validate([
            'natureza_operacao' => 'required|string|max:60',
            'tipo_documento' => 'required|in:0,1',
            'finalidade_emissao' => 'required|in:1,2,3,4',
            'data_emissao' => 'required|date',
            'nome_destinatario' => 'required|string|max:60',
            'uf_destinatario' => 'required|string|size:2',
            'cep_destinatario' => 'required|string',
            'municipio_destinatario' => 'required|string',
            'bairro_destinatario' => 'required|string',
            'logradouro_destinatario' => 'required|string',
            'numero_destinatario' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Limpar mensagem de erro se existir
            if ($nfe->status === 'erro') {
                $nfe->mensagem_erro = null;
                $nfe->status = 'rascunho';
            }

            // Atualizar dados básicos da NFe
            $nfe->natureza_operacao = $request->natureza_operacao;
            $nfe->tipo_documento = $request->tipo_documento;
            $nfe->finalidade_emissao = $request->finalidade_emissao;
            $nfe->data_emissao = $request->data_emissao;
            
            // Dados do destinatário
            $nfe->nome_destinatario = $request->nome_destinatario;
            $nfe->uf_destinatario = $request->uf_destinatario;
            $nfe->cep_destinatario = preg_replace('/\D/', '', $request->cep_destinatario);
            $nfe->municipio_destinatario = $request->municipio_destinatario;
            $nfe->bairro_destinatario = $request->bairro_destinatario;
            $nfe->logradouro_destinatario = $request->logradouro_destinatario;
            $nfe->numero_destinatario = $request->numero_destinatario;

            // CPF ou CNPJ
            if ($request->filled('cpf_destinatario')) {
                $nfe->cpf_destinatario = preg_replace('/\D/', '', $request->cpf_destinatario);
                $nfe->cnpj_destinatario = null;
                $nfe->ie_destinatario = null;
                $nfe->indicador_ie_destinatario = '9'; // Não contribuinte
            } else {
                $nfe->cnpj_destinatario = preg_replace('/\D/', '', $request->cnpj_destinatario);
                $nfe->cpf_destinatario = null;
                $nfe->ie_destinatario = $request->ie_destinatario;
                $nfe->indicador_ie_destinatario = $request->indicador_ie_destinatario;
            }

            // Calcular local_destino baseado na UF
            $empresa = Auth::user()->company;
            $nfe->local_destino = ($nfe->uf_destinatario === $empresa->uf) ? 1 : 2;
            
            $nfe->save();

            DB::commit();

            // Verificar se deve apenas salvar ou salvar e emitir
            $action = $request->input('action', 'save');
            
            if ($action === 'emit') {
                // Redirecionar para emissão
                return redirect()->route('nfe.emitir', $nfe)
                    ->with('success', 'NFe atualizada! Emitindo agora...');
            }

            return redirect()->route('nfe.show', $nfe)
                ->with('success', 'NFe atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Erro ao atualizar NFe: ' . $e->getMessage());
            
            return back()->withInput()
                ->with('error', 'Erro ao atualizar NFe: ' . $e->getMessage());
        }
    }

    /**
     * Emitir NFe via API Focus NFe
     */
    public function emitir(Request $request, Nfe $nfe = null)
    {
        \Log::info('Método emitir chamado', ['nfe_id' => $nfe?->id, 'request' => $request->all()]);
        
        if ($nfe === null) {
            $nfe = Nfe::findOrFail($request->route('nfe'));
        }

        if ($nfe->status === 'emitida') {
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Esta NFe já foi emitida.');
        }

        try {
            // Limpar mensagem de erro anterior ao reenviar
            $nfe->status = 'processando';
            $nfe->mensagem_erro = null;
            $nfe->save();

            $response = $this->focusNfeService->emitirNfe($nfe);

            if ($response['sucesso']) {
                $nfe->status = 'emitida';
                $nfe->numero_nfe = $response['dados']['numero'] ?? null;
                $nfe->chave_nfe = $response['dados']['chave_nfe'] ?? null;
                $nfe->status_sefaz = $response['dados']['status'] ?? null;
                $nfe->mensagem_erro = null;
            } else {
                $nfe->status = 'erro';
                $nfe->mensagem_erro = $response['mensagem'] ?? 'Erro desconhecido';
            }

            $nfe->save();

            if ($response['sucesso']) {
                return redirect()->route('nfe.show', $nfe)
                    ->with('success', 'NFe emitida com sucesso!');
            } else {
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'Erro ao emitir NFe: ' . $response['mensagem']);
            }
        } catch (\Exception $e) {
            $nfe->status = 'erro';
            $nfe->mensagem_erro = $e->getMessage();
            $nfe->save();

            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Erro ao emitir NFe: ' . $e->getMessage());
        }
    }

    /**
     * Consultar NFe na API
     */
    public function consultar(Nfe $nfe)
    {
        try {
            $response = $this->focusNfeService->consultarNfe($nfe->numero);
            
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Baixar DANFE
     */
    public function danfe(Nfe $nfe)
    {
        \Log::info('Controller DANFE - Iniciando', [
            'nfe_id' => $nfe->id,
            'nfe_ref' => $nfe->ref,
            'nfe_status' => $nfe->status
        ]);
        
        if (!in_array($nfe->status, ['autorizado', 'emitida'])) {
            \Log::warning('Controller DANFE - Status inválido', [
                'nfe_status' => $nfe->status,
                'required_status' => ['autorizado', 'emitida']
            ]);
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'DANFE disponível apenas para NFes autorizadas.');
        }

        try {
            $pdf = $this->focusNfeService->baixarDanfe($nfe->ref);
            
            \Log::info('Controller DANFE - PDF recebido', [
                'nfe_ref' => $nfe->ref,
                'pdf_size' => strlen($pdf),
                'is_pdf' => substr($pdf, 0, 4) === '%PDF'
            ]);
            
            // Retornar PDF como blob inline para visualização no navegador
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="danfe-' . $nfe->ref . '.pdf"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            \Log::error('Controller DANFE - Erro', [
                'nfe_ref' => $nfe->ref,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Erro ao baixar DANFE: ' . $e->getMessage());
        }
    }

    /**
     * Baixar XML
     */
    public function xml(Nfe $nfe)
    {
        // Desabilitar logs durante this request to avoid any output
        config(['logging.default' => 'null']);
        
        if (!in_array($nfe->status, ['autorizado', 'emitida'])) {
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'XML disponível apenas para NFes autorizadas.');
        }

        try {
            // Limpar qualquer output buffer que possa interferir ANTES de fazer qualquer coisa
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            $xml = $this->focusNfeService->baixarXml($nfe->ref);
            
            // Garantir que não há nada sendo enviado para o output
            if (ob_get_level()) {
                ob_clean();
            }
            
            // Retornar XML simples como funcionava antes
            $disposition = request()->get('download') ? 'attachment' : 'inline';
            
            return response($xml)
                ->header('Content-Type', 'text/xml; charset=utf-8')
                ->header('Content-Disposition', $disposition . '; filename="nfe-' . $nfe->ref . '.xml"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
                
        } catch (\Exception $e) {
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Erro ao baixar XML: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar NFe
     */
    public function cancelar(Request $request, Nfe $nfe)
    {
        $request->validate([
            'justificativa' => 'required|string|min:15|max:255'
        ]);

        if (!in_array($nfe->status, ['emitida', 'autorizado'])) {
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Apenas NFes emitidas podem ser canceladas.');
        }

        try {
            \Log::info('Iniciando cancelamento NFe normal', [
                'nfe_id' => $nfe->id,
                'ref' => $nfe->ref,
                'justificativa' => $request->justificativa
            ]);

            $response = $this->focusNfeService->cancelarNfe($nfe->ref, $request->justificativa);

            \Log::info('Resultado cancelamento NFe normal', [
                'nfe_id' => $nfe->id,
                'ref' => $nfe->ref,
                'response' => $response
            ]);

            // Verificar se cancelamento foi bem-sucedido
            $cancelamentoOK = ($response['sucesso'] ?? false) || 
                            ($response['status'] ?? '') === 'cancelado' ||
                            (isset($response['status_sefaz']) && $response['status_sefaz'] === '135');

            if ($cancelamentoOK) {
                $nfe->status = 'cancelado';
                $nfe->justificativa_cancelamento = $request->justificativa;
                $nfe->save();

                \Log::info('NFe cancelada via modal - status atualizado', [
                    'nfe_id' => $nfe->id,
                    'novo_status' => 'cancelado'
                ]);

                return redirect()->route('nfe.show', $nfe)
                    ->with('success', 'NFe cancelada com sucesso!');
            } else {
                $mensagemErro = $response['mensagem'] ?? $response['erro'] ?? $response['mensagem_sefaz'] ?? 'Erro desconhecido ao cancelar NFe';
                return redirect()->route('nfe.show', $nfe)
                    ->with('error', 'Erro ao cancelar NFe: ' . $mensagemErro);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao cancelar NFe normal', [
                'nfe_id' => $nfe->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Erro ao cancelar NFe: ' . $e->getMessage());
        }
    }

    /**
     * Excluir NFe
     */
    public function destroy(Nfe $nfe)
    {
        if ($nfe->status === 'emitida') {
            return redirect()->route('nfe.index')
                ->with('error', 'NFes emitidas não podem ser excluídas.');
        }

        try {
            $nfe->items()->delete();
            $nfe->delete();

            return redirect()->route('nfe.index')
                ->with('success', 'NFe excluída com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('nfe.index')
                ->with('error', 'Erro ao excluir NFe: ' . $e->getMessage());
        }
    }

    /**
     * Criar NFe de teste para homologação
     */
    public function criarTeste()
    {
        try {
            // Criar NFe de teste
            $nfe = new Nfe();
            $nfe->company_id = 1; // Usar company_id fixo para teste
            $nfe->ref = 'TESTE_' . time(); // Referência única para teste
            $nfe->numero_nfe = '999'; // Número de teste
            $nfe->serie_nfe = '1';
            $nfe->natureza_operacao = 'Venda para teste de homologação';
            $nfe->presenca_comprador = 1; // Operação presencial
            $nfe->finalidade_emissao = 4; // NFe de homologação
            $nfe->consumidor_final = 1; // Consumidor final
            $nfe->tipo_documento = '1'; // Saída
            $nfe->local_destino = '1'; // Operação interna
            $nfe->modalidade_frete = '9'; // Sem frete
            
            // Dados do emitente (empresa) - usando dados reais da empresa
            $nfe->cnpj_emitente = '61196441000103'; // CNPJ real da empresa
            $nfe->nome_emitente = 'GUABINORTE COMERCIO DE RACAO ANIMAL LTDA';
            $nfe->ie_emitente = '750328711';
            $nfe->logradouro_emitente = 'ROD BR 316';
            $nfe->numero_emitente = 'S/N';
            $nfe->bairro_emitente = 'SANTA ROSA';
            $nfe->municipio_emitente = 'BENEVIDES';
            $nfe->uf_emitente = 'PA';
            $nfe->cep_emitente = '68795000';
            $nfe->regime_tributario_emitente = '1'; // Simples Nacional
            
            // Dados do destinatário (teste)
            $nfe->cpf_destinatario = '11111111111';
            $nfe->nome_destinatario = 'CLIENTE TESTE HOMOLOGACAO';
            $nfe->logradouro_destinatario = 'RUA TESTE';
            $nfe->numero_destinatario = '123';
            $nfe->bairro_destinatario = 'CENTRO';
            $nfe->municipio_destinatario = 'SAO PAULO';
            $nfe->uf_destinatario = 'SP';
            $nfe->cep_destinatario = '01000000';
            $nfe->ie_destinatario = null;
            $nfe->indicador_ie_destinatario = '9'; // Não contribuinte
            
            // Totais (valores zerados para teste)
            $nfe->valor_produtos = 100.00;
            $nfe->valor_frete = 0.00;
            $nfe->valor_seguro = 0.00;
            $nfe->valor_desconto = 0.00;
            $nfe->valor_outras_despesas = 0.00;
            $nfe->valor_pis = 0.65;
            $nfe->valor_cofins = 3.00;
            $nfe->valor_icms = 0.00;
            $nfe->valor_total = 100.00;
            
            $nfe->status = 'processando_autorizacao';
            $nfe->data_emissao = now();
            $nfe->save();

            // Criar item de teste
            $item = new NfeItem();
            $item->nfe_id = $nfe->id;
            $item->numero_item = 1;
            $item->codigo_produto = 'TESTE001';
            $item->descricao = 'PRODUTO PARA TESTE DE HOMOLOGACAO';
            $item->codigo_ncm = '94036000';
            $item->cfop = '5102';
            $item->unidade_comercial = 'UN';
            $item->quantidade_comercial = 1.00;
            $item->valor_unitario_comercial = 100.00;
            $item->valor_bruto_produtos = 100.00;
            $item->valor_desconto = 0.00;
            $item->valor_frete = 0.00;
            $item->valor_seguro = 0.00;
            $item->valor_outras_despesas = 0.00;
            $item->valor_total_item = 100.00;
            
            // Impostos
            $item->icms_origem = '0';
            $item->icms_situacao_tributaria = '040';
            $item->icms_base_calculo = 0.00;
            $item->icms_aliquota = 0.00;
            $item->icms_valor = 0.00;
            
            $item->ipi_situacao_tributaria = '53';
            $item->ipi_base_calculo = 0.00;
            $item->ipi_aliquota = 0.00;
            $item->ipi_valor = 0.00;
            
            $item->pis_situacao_tributaria = '01';
            $item->pis_base_calculo = 100.00;
            $item->pis_aliquota = 0.65;
            $item->pis_valor = 0.65;
            
            $item->cofins_situacao_tributaria = '01';
            $item->cofins_base_calculo = 100.00;
            $item->cofins_aliquota = 3.00;
            $item->cofins_valor = 3.00;
            
            $item->save();

            // Recalcular totais
            $nfe->recalcularTotais();
            $nfe->save();

            // Mostrar dados que serão enviados para Focus NFe
            $dadosEnvio = $this->focusNfeService->montarDadosParaVisualizacao($nfe);

            // Tentar emitir via Focus NFe usando o CNPJ de teste
            try {
                $response = $this->focusNfeService->emitirNfe($nfe);
                
                if ($response['sucesso']) {
                    $nfe->status = 'autorizado';
                    $nfe->chave_nfe = $response['chave'] ?? null;
                    $nfe->status_sefaz = $response['protocolo'] ?? null;
                    $nfe->save();
                    
                    return response()->json([
                        'sucesso' => true,
                        'mensagem' => 'NFe de teste criada e EMITIDA com sucesso!',
                        'nfe_id' => $nfe->id,
                        'chave' => $nfe->chave_nfe,
                        'protocolo' => $nfe->status_sefaz,
                        'dados_enviados' => $dadosEnvio,
                        'detalhes' => [
                            'numero' => $nfe->numero_nfe,
                            'serie' => $nfe->serie_nfe,
                            'valor_total' => $nfe->valor_total,
                            'cnpj_emitente' => $nfe->cnpj_emitente,
                            'nome_emitente' => $nfe->nome_emitente,
                            'destinatario' => $nfe->nome_destinatario,
                            'status' => $nfe->status,
                            'ambiente' => 'homologacao',
                            'pis_aliquota' => '0.65%',
                            'cofins_aliquota' => '3.00%',
                            'icms_cst' => '040 (não tributado)'
                        ],
                        'focus_response' => $response
                    ]);
                } else {
                    $nfe->status = 'erro_autorizacao';
                    $nfe->mensagem_sefaz = $response['mensagem'] ?? 'Erro desconhecido';
                    $nfe->save();
                    
                    return response()->json([
                        'sucesso' => false,
                        'mensagem' => 'NFe criada mas houve erro ao emitir: ' . ($response['mensagem'] ?? 'Erro desconhecido'),
                        'nfe_id' => $nfe->id,
                        'dados_enviados' => $dadosEnvio,
                        'detalhes' => [
                            'numero' => $nfe->numero_nfe,
                            'serie' => $nfe->serie_nfe,
                            'cnpj_emitente' => $nfe->cnpj_emitente,
                            'status' => $nfe->status,
                            'erro_sefaz' => $nfe->mensagem_sefaz
                        ],
                        'focus_response' => $response,
                        'proximos_passos' => [
                            'NFe foi salva no banco de dados',
                            'Verifique se o CNPJ está autorizado no Focus NFe',
                            'Acesse /nfe/' . $nfe->id . ' para ver detalhes'
                        ]
                    ]);
                }
            } catch (\Exception $emitirException) {
                $nfe->status = 'erro_autorizacao';
                $nfe->mensagem_sefaz = 'Erro ao conectar com Focus NFe: ' . $emitirException->getMessage();
                $nfe->save();
                
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'NFe criada mas houve erro de conexão: ' . $emitirException->getMessage(),
                    'nfe_id' => $nfe->id,
                    'dados_enviados' => $dadosEnvio,
                    'detalhes' => [
                        'numero' => $nfe->numero_nfe,
                        'cnpj_emitente' => $nfe->cnpj_emitente,
                        'status' => $nfe->status,
                        'erro_conexao' => $emitirException->getMessage()
                    ],
                    'proximos_passos' => [
                        'NFe foi salva no banco de dados',
                        'Verifique a configuração do Focus NFe',
                        'Acesse /nfe/' . $nfe->id . ' para ver detalhes'
                    ]
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro interno: ' . $e->getMessage(),
                'erro' => $e->getTrace()
            ]);
        }
    }

    /**
     * Buscar produtos para NFe
     */
    public function buscarProdutos()
    {
        $produtos = Product::where('company_id', Auth::user()->company_id ?? 1)
                          ->select('id', 'name', 'sale_price', 'codigo', 'ncm', 'internal_code')
                          ->orderBy('name')
                          ->get();
        
        return response()->json($produtos);
    }

    /**
     * Buscar produtos via AJAX
     */
    public function buscarProdutosAjax(Request $request)
    {
        $query = $request->get('q', '');
        
        $produtos = Product::where('company_id', Auth::user()->company_id ?? 1)
                          ->where(function($q) use ($query) {
                              $q->where('name', 'like', "%{$query}%")
                                ->orWhere('codigo', 'like', "%{$query}%")
                                ->orWhere('internal_code', 'like', "%{$query}%");
                          })
                          ->select('id', 'name', 'sale_price', 'codigo', 'ncm', 'internal_code', 'unit')
                          ->limit(20)
                          ->get();
        
        return response()->json($produtos);
    }
    
    /**
     * Consultar último número de NFe na API Focus NFe
     */
    public function consultarUltimoNumero(Request $request)
    {
        try {
            $serie = $request->get('serie', 1); // Série padrão 1
            
            Log::info('Consultando último número NFe na API', [
                'serie' => $serie,
                'user_id' => Auth::id(),
                'company_id' => Auth::user()->company_id
            ]);
            
            $resultado = $this->focusNfeService->obterUltimoNumeroNfe($serie);
            
            Log::info('Resultado consulta último número NFe', $resultado);
            
            if ($resultado['sucesso']) {
                // Também consultar no banco local para comparação
                $ultimoLocal = Nfe::where('company_id', Auth::user()->company_id)
                                 ->where('serie_nfe', $serie)
                                 ->whereNotNull('numero_nfe')
                                 ->orderBy('numero_nfe', 'desc')
                                 ->first();
                
                $ultimoNumeroLocal = $ultimoLocal ? intval($ultimoLocal->numero_nfe) : 0;
                
                return response()->json([
                    'sucesso' => true,
                    'api' => $resultado,
                    'banco_local' => [
                        'ultimo_numero' => $ultimoNumeroLocal,
                        'proximo_numero' => $ultimoNumeroLocal + 1
                    ],
                    'comparacao' => [
                        'api_maior' => $resultado['ultimo_numero'] > $ultimoNumeroLocal,
                        'diferenca' => abs($resultado['ultimo_numero'] - $ultimoNumeroLocal)
                    ],
                    'recomendacao' => [
                        'proximo_numero_sugerido' => max($resultado['proximo_numero'], $ultimoNumeroLocal + 1),
                        'fonte' => $resultado['ultimo_numero'] > $ultimoNumeroLocal ? 'API Focus NFe' : 'Banco Local'
                    ]
                ]);
            } else {
                return response()->json([
                    'sucesso' => false,
                    'erro' => $resultado['erro'],
                    'fallback' => [
                        'ultimo_numero_local' => $ultimoLocal ? intval($ultimoLocal->numero_nfe) : 0,
                        'proximo_numero_local' => $ultimoLocal ? intval($ultimoLocal->numero_nfe) + 1 : 1
                    ]
                ], 400);
            }
            
        } catch (\Exception $e) {
            Log::error('Erro ao consultar último número NFe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'sucesso' => false,
                'erro' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sincronizar numeração com a API
     */
    public function sincronizarNumeracao(Request $request)
    {
        try {
            $serie = $request->get('serie', 1);
            
            // Consultar API
            $resultadoApi = $this->focusNfeService->obterUltimoNumeroNfe($serie);
            
            if (!$resultadoApi['sucesso']) {
                return response()->json([
                    'sucesso' => false,
                    'erro' => 'Não foi possível consultar a API: ' . $resultadoApi['erro']
                ], 400);
            }
            
            // Atualizar próximo número no banco local se necessário
            $proximoNumero = $resultadoApi['proximo_numero'];
            
            // Log da sincronização
            Log::info('Sincronização de numeração NFe', [
                'serie' => $serie,
                'ultimo_numero_api' => $resultadoApi['ultimo_numero'],
                'proximo_numero_api' => $proximoNumero,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'sucesso' => true,
                'mensagem' => 'Numeração sincronizada com sucesso',
                'dados' => [
                    'serie' => $serie,
                    'ultimo_numero_api' => $resultadoApi['ultimo_numero'],
                    'proximo_numero_sugerido' => $proximoNumero
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar numeração NFe', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'sucesso' => false,
                'erro' => 'Erro interno: ' . $e->getMessage()
            ], 500);
        }
    }
}


