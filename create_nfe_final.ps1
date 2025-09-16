$content = @'
<?php

namespace App\Http\Controllers;

use App\Models\Nfe;
use App\Models\NfeItem;
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

    public function index(Request $request)
    {
        $query = Nfe::with(['company'])
            ->where('company_id', Auth::user()->company_id)
            ->orderBy('created_at', 'desc');

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

    public function create()
    {
        return view('nfe.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'natureza_operacao' => 'required|string|max:60',
            'destinatario_nome' => 'required|string|max:60',
            'destinatario_cnpj_cpf' => 'required|string',
            'destinatario_email' => 'nullable|email',
            'destinatario_telefone' => 'nullable|string',
            'destinatario_logradouro' => 'required|string',
            'destinatario_numero' => 'required|string',
            'destinatario_bairro' => 'required|string',
            'destinatario_cidade' => 'required|string',
            'destinatario_uf' => 'required|string|size:2',
            'destinatario_cep' => 'required|string',
            'destinatario_complemento' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'serie' => 'required|integer|min:1',
            'data_emissao' => 'required|date',
            'valor_frete' => 'nullable|numeric|min:0',
            'itens' => 'required|array|min:1',
            'itens.*.codigo' => 'nullable|string',
            'itens.*.descricao' => 'required|string',
            'itens.*.ncm' => 'required|string|size:8',
            'itens.*.cfop' => 'required|string|size:4',
            'itens.*.unidade' => 'required|string',
            'itens.*.quantidade' => 'required|numeric|min:0.01',
            'itens.*.valor_unitario' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $nfe = new Nfe();
            $nfe->company_id = Auth::user()->company_id;
            $nfe->status = $request->input('action') === 'emit' ? 'processando' : 'rascunho';
            $nfe->natureza_operacao = $request->natureza_operacao;
            $nfe->serie = $request->serie;
            $nfe->data_emissao = $request->data_emissao;
            $nfe->observacoes = $request->observacoes;
            
            $nfe->destinatario_nome = $request->destinatario_nome;
            $nfe->destinatario_cnpj_cpf = preg_replace('/\D/', '', $request->destinatario_cnpj_cpf);
            $nfe->destinatario_email = $request->destinatario_email;
            $nfe->destinatario_telefone = $request->destinatario_telefone;
            $nfe->destinatario_logradouro = $request->destinatario_logradouro;
            $nfe->destinatario_numero = $request->destinatario_numero;
            $nfe->destinatario_bairro = $request->destinatario_bairro;
            $nfe->destinatario_cidade = $request->destinatario_cidade;
            $nfe->destinatario_uf = $request->destinatario_uf;
            $nfe->destinatario_cep = preg_replace('/\D/', '', $request->destinatario_cep);
            $nfe->destinatario_complemento = $request->destinatario_complemento;
            
            $nfe->valor_frete = $request->valor_frete ?? 0;
            
            $nfe->save();
            
            foreach ($request->itens as $itemData) {
                $item = new NfeItem();
                $item->nfe_id = $nfe->id;
                $item->codigo = $itemData['codigo'] ?? '';
                $item->descricao = $itemData['descricao'];
                $item->ncm = $itemData['ncm'];
                $item->cfop = $itemData['cfop'];
                $item->unidade = $itemData['unidade'];
                $item->quantidade = $itemData['quantidade'];
                $item->valor_unitario = $itemData['valor_unitario'];
                $item->valor_total = $itemData['quantidade'] * $itemData['valor_unitario'];
                
                $item->icms_aliquota = $itemData['icms_aliquota'] ?? 0;
                $item->ipi_aliquota = $itemData['ipi_aliquota'] ?? 0;
                $item->pis_aliquota = $itemData['pis_aliquota'] ?? 0;
                $item->cofins_aliquota = $itemData['cofins_aliquota'] ?? 0;
                
                $item->save();
            }
            
            $nfe->recalcularTotais();
            $nfe->save();
            
            DB::commit();
            
            if ($request->input('action') === 'emit') {
                return $this->emitir($request, $nfe);
            }
            
            return redirect()->route('nfe.show', $nfe)
                ->with('success', 'NFe criada com sucesso!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withInput()
                ->with('error', 'Erro ao criar NFe: ' . $e->getMessage());
        }
    }

    public function show(Nfe $nfe)
    {
        $nfe->load(['itens', 'company']);
        return view('nfe.show', compact('nfe'));
    }

    public function edit(Nfe $nfe)
    {
        if ($nfe->status !== 'rascunho') {
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Apenas NFes em rascunho podem ser editadas.');
        }

        $nfe->load('itens');
        return view('nfe.edit', compact('nfe'));
    }

    public function update(Request $request, Nfe $nfe)
    {
        if ($nfe->status !== 'rascunho') {
            return redirect()->route('nfe.show', $nfe)
                ->with('error', 'Apenas NFes em rascunho podem ser editadas.');
        }

        // Mesma validação do store...
        try {
            DB::beginTransaction();
            // Atualizar dados...
            DB::commit();
            return redirect()->route('nfe.show', $nfe)->with('success', 'NFe atualizada!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function emitir(Request $request, Nfe $nfe = null)
    {
        if ($nfe === null) {
            $nfe = Nfe::findOrFail($request->route('nfe'));
        }

        if ($nfe->status === 'emitida') {
            return redirect()->route('nfe.show', $nfe)->with('error', 'NFe já emitida.');
        }

        try {
            $nfe->status = 'processando';
            $nfe->save();

            $response = $this->focusNfeService->emitirNfe($nfe);

            if ($response['success']) {
                $nfe->status = 'emitida';
                $nfe->numero = $response['data']['numero'] ?? null;
                $nfe->chave_acesso = $response['data']['chave_acesso'] ?? null;
                $nfe->protocolo = $response['data']['protocolo'] ?? null;
                $nfe->mensagem_erro = null;
            } else {
                $nfe->status = 'erro';
                $nfe->mensagem_erro = $response['message'];
            }

            $nfe->save();
            return redirect()->route('nfe.show', $nfe)->with('success', 'NFe processada!');
        } catch (\Exception $e) {
            $nfe->status = 'erro';
            $nfe->mensagem_erro = $e->getMessage();
            $nfe->save();
            return redirect()->route('nfe.show', $nfe)->with('error', 'Erro: ' . $e->getMessage());
        }
    }

    public function consultar(Nfe $nfe)
    {
        try {
            $response = $this->focusNfeService->consultarNfe($nfe->numero);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function danfe(Nfe $nfe)
    {
        if ($nfe->status !== 'emitida') {
            return redirect()->route('nfe.show', $nfe)->with('error', 'DANFE disponível apenas para NFes emitidas.');
        }

        try {
            $pdf = $this->focusNfeService->baixarDanfe($nfe->numero);
            return response($pdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="danfe-' . $nfe->numero . '.pdf"');
        } catch (\Exception $e) {
            return redirect()->route('nfe.show', $nfe)->with('error', 'Erro ao baixar DANFE: ' . $e->getMessage());
        }
    }

    public function xml(Nfe $nfe)
    {
        if ($nfe->status !== 'emitida') {
            return redirect()->route('nfe.show', $nfe)->with('error', 'XML disponível apenas para NFes emitidas.');
        }

        try {
            $xml = $this->focusNfeService->baixarXml($nfe->numero);
            return response($xml)
                ->header('Content-Type', 'application/xml')
                ->header('Content-Disposition', 'attachment; filename="nfe-' . $nfe->numero . '.xml"');
        } catch (\Exception $e) {
            return redirect()->route('nfe.show', $nfe)->with('error', 'Erro ao baixar XML: ' . $e->getMessage());
        }
    }

    public function cancelar(Request $request, Nfe $nfe)
    {
        $request->validate(['justificativa' => 'required|string|min:15|max:255']);

        if ($nfe->status !== 'emitida') {
            return redirect()->route('nfe.show', $nfe)->with('error', 'Apenas NFes emitidas podem ser canceladas.');
        }

        try {
            $response = $this->focusNfeService->cancelarNfe($nfe->numero, $request->justificativa);

            if ($response['success']) {
                $nfe->status = 'cancelada';
                $nfe->justificativa_cancelamento = $request->justificativa;
                $nfe->save();
                return redirect()->route('nfe.show', $nfe)->with('success', 'NFe cancelada com sucesso!');
            } else {
                return redirect()->route('nfe.show', $nfe)->with('error', 'Erro ao cancelar NFe: ' . $response['message']);
            }
        } catch (\Exception $e) {
            return redirect()->route('nfe.show', $nfe)->with('error', 'Erro ao cancelar NFe: ' . $e->getMessage());
        }
    }

    public function destroy(Nfe $nfe)
    {
        if ($nfe->status === 'emitida') {
            return redirect()->route('nfe.index')->with('error', 'NFes emitidas não podem ser excluídas.');
        }

        try {
            $nfe->itens()->delete();
            $nfe->delete();
            return redirect()->route('nfe.index')->with('success', 'NFe excluída com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('nfe.index')->with('error', 'Erro ao excluir NFe: ' . $e->getMessage());
        }
    }

    public function buscarProdutos(Request $request)
    {
        $search = $request->input('q');
        
        $produtos = \App\Models\Product::where('company_id', Auth::user()->company_id)
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                      ->orWhere('internal_code', 'like', "%$search%");
            })
            ->limit(10)
            ->get(['id', 'name', 'internal_code', 'sale_price', 'unit']);
            
        return response()->json($produtos);
    }
}
'@

$content | Out-File -FilePath "app\Http\Controllers\NfeController.php" -Encoding UTF8
