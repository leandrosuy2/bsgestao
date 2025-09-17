<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use App\Models\Nfe;
use App\Models\Company;

class FocusNfeService
{
    private $client;
    private $baseUrl;
    private $token;

    public function __construct()
    {
        $environment = config('services.focus_nfe.environment', 'sandbox');
        
        // URL base conforme ambiente
        if ($environment === 'homologacao' || $environment === 'sandbox') {
            $this->baseUrl = 'https://homologacao.focusnfe.com.br';
        } else {
            $this->baseUrl = 'https://api.focusnfe.com.br';
        }
        
        $this->token = config('services.focus_nfe.token');
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->token . ':')
            ]
        ]);
    }

    /**
     * Emitir NFe
     */
    public function emitirNfe(Nfe $nfe)
    {
        try {
            $dados = $this->montarDadosNfe($nfe);
            
            Log::info('Focus NFe - Dados enviados para API', $dados);
            
            $response = $this->client->post("/v2/nfe?ref={$nfe->ref}", [
                'json' => $dados
            ]);

            $resultado = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Focus NFe - Resposta da emissão', $resultado);
            
            // Processar resposta e retornar formato padronizado
            $processado = $this->processarRespostaEmissao($nfe, $resultado);
            
            // Verificar se houve sucesso baseado no status HTTP e conteúdo
            if ($response->getStatusCode() === 200 || $response->getStatusCode() === 201) {
                if (isset($resultado['status']) && $resultado['status'] === 'autorizado') {
                    return [
                        'sucesso' => true,
                        'dados' => $resultado,
                        'chave' => $resultado['chave_nfe'] ?? null,
                        'protocolo' => $resultado['protocolo'] ?? null
                    ];
                } else {
                    return [
                        'sucesso' => false,
                        'erro' => $resultado,
                        'mensagem' => $resultado['mensagem_sefaz'] ?? 'Erro na autorização da NFe'
                    ];
                }
            } else {
                return [
                    'sucesso' => false,
                    'erro' => $resultado,
                    'mensagem' => 'Erro HTTP: ' . $response->getStatusCode()
                ];
            }
            
        } catch (RequestException $e) {
            $errorBody = null;
            if ($e->hasResponse()) {
                $errorBody = $e->getResponse()->getBody()->getContents();
                $errorData = json_decode($errorBody, true);
                
                Log::error('Focus NFe - Erro na emissão', [
                    'error' => $e->getMessage(),
                    'response' => $errorBody,
                    'status_code' => $e->getResponse()->getStatusCode()
                ]);
                
                return [
                    'sucesso' => false,
                    'erro' => $errorData ?: ['message' => $errorBody],
                    'mensagem' => isset($errorData['message']) ? $errorData['message'] : $e->getMessage()
                ];
            }
            
            Log::error('Focus NFe - Erro na emissão', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'sucesso' => false,
                'erro' => ['message' => $e->getMessage()],
                'mensagem' => $e->getMessage()
            ];
        }
    }

    /**
     * Consultar status da NFe
     */
    public function consultarNfe($ref)
    {
        try {
            $response = $this->client->get("/v2/nfe/{$ref}");
            $resultado = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Focus NFe - Consulta NFe', $resultado);
            
            return $resultado;
            
        } catch (RequestException $e) {
            Log::error('Focus NFe - Erro na consulta', [
                'ref' => $ref,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Erro ao consultar NFe: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar NFe
     */
    public function cancelarNfe($ref, $justificativa)
    {
        try {
            $dados = [
                'justificativa' => $justificativa
            ];
            
            $response = $this->client->delete("/v2/nfe/{$ref}", [
                'json' => $dados
            ]);

            $resultado = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Focus NFe - Cancelamento', $resultado);
            
            return $resultado;
            
        } catch (RequestException $e) {
            Log::error('Focus NFe - Erro no cancelamento', [
                'ref' => $ref,
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Erro ao cancelar NFe: ' . $e->getMessage());
        }
    }

    /**
     * Baixar DANFE
     */
    public function baixarDanfe($ref)
    {
        try {
            Log::info('Focus NFe - Iniciando download DANFE', ['ref' => $ref]);
            
            // Primeiro, consultar a NFe para obter informações de download
            $response = $this->client->get("/v2/nfe/{$ref}");
            $nfeData = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Focus NFe - Dados da NFe consultada', [
                'ref' => $ref,
                'status' => $nfeData['status'] ?? 'indefinido',
                'campos_disponiveis' => array_keys($nfeData)
            ]);
            
            // Verificar se existe o caminho do DANFE
            if (isset($nfeData['caminho_danfe'])) {
                $caminhoRelativo = $nfeData['caminho_danfe'];
                
                // Construir URL completa
                // A URL base para downloads é diferente da API
                $urlCompleta = 'https://api.focusnfe.com.br' . $caminhoRelativo;
                
                Log::info('Focus NFe - URL DANFE construída', [
                    'caminho_relativo' => $caminhoRelativo,
                    'url_completa' => $urlCompleta
                ]);
                
                // Fazer download do PDF
                $pdfResponse = $this->client->get($urlCompleta);
                $pdfContent = $pdfResponse->getBody()->getContents();
                
                Log::info('Focus NFe - PDF baixado com sucesso', [
                    'url' => $urlCompleta,
                    'tamanho' => strlen($pdfContent),
                    'e_pdf' => substr($pdfContent, 0, 4) === '%PDF'
                ]);
                
                return $pdfContent;
            }
            
            // Se não encontrou o caminho, tentar métodos alternativos
            Log::info('Focus NFe - Caminho DANFE não encontrado, tentando endpoints diretos');
            
            // Procurar URL do DANFE nos dados retornados
            $danfeUrl = null;
            
            // Verificar vários campos possíveis onde pode estar a URL
            $camposPossiveis = ['danfe_pdf', 'pdf_url', 'url_danfe', 'caminho_pdf', 'path_pdf'];
            foreach ($camposPossiveis as $campo) {
                if (isset($nfeData[$campo]) && filter_var($nfeData[$campo], FILTER_VALIDATE_URL)) {
                    $danfeUrl = $nfeData[$campo];
                    Log::info('Focus NFe - URL DANFE encontrada', ['campo' => $campo, 'url' => $danfeUrl]);
                    break;
                }
            }
            
            // Se encontrou a URL, fazer download direto
            if ($danfeUrl) {
                $pdfResponse = $this->client->get($danfeUrl);
                return $pdfResponse->getBody()->getContents();
            }
            
            // Tentar endpoints alternativos
            try {
                $response = $this->client->get("/v2/nfe/{$ref}.pdf");
                return $response->getBody()->getContents();
            } catch (RequestException $e) {
                if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                    // Tentar endpoint /danfe
                    $response = $this->client->get("/v2/nfe/{$ref}/danfe");
                    return $response->getBody()->getContents();
                } else {
                    throw $e;
                }
            }
            
        } catch (RequestException $e) {
            Log::error('Focus NFe - Erro ao baixar DANFE', [
                'ref' => $ref,
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);
            
            throw new \Exception('Erro ao baixar DANFE: ' . $e->getMessage());
        }
    }

    /**
     * Baixar XML
     */
    public function baixarXml($ref)
    {
        try {
            Log::info('Focus NFe - Iniciando download XML', ['ref' => $ref]);
            
            // Primeiro, consultar a NFe para obter informações de download
            $response = $this->client->get("/v2/nfe/{$ref}");
            $nfeData = json_decode($response->getBody()->getContents(), true);
            
            // Verificar se existe o caminho do XML
            if (isset($nfeData['caminho_xml_nota_fiscal'])) {
                $caminhoRelativo = $nfeData['caminho_xml_nota_fiscal'];
                
                // Construir URL completa
                $urlCompleta = 'https://api.focusnfe.com.br' . $caminhoRelativo;
                
                Log::info('Focus NFe - URL XML construída', [
                    'caminho_relativo' => $caminhoRelativo,
                    'url_completa' => $urlCompleta
                ]);
                
                // Fazer download do XML
                $xmlResponse = $this->client->get($urlCompleta);
                $xmlContent = $xmlResponse->getBody()->getContents();
                
                Log::info('Focus NFe - XML baixado com sucesso', [
                    'url' => $urlCompleta,
                    'tamanho' => strlen($xmlContent)
                ]);
                
                return $xmlContent;
            }
            
            // Se não encontrou o caminho, tentar métodos alternativos
            Log::info('Focus NFe - Caminho XML não encontrado, tentando endpoints diretos');
            
            // Procurar URL do XML nos dados retornados
            $xmlUrl = null;
            
            // Verificar vários campos possíveis onde pode estar a URL
            $camposPossiveis = ['xml_url', 'url_xml', 'caminho_xml', 'path_xml'];
            foreach ($camposPossiveis as $campo) {
                if (isset($nfeData[$campo]) && filter_var($nfeData[$campo], FILTER_VALIDATE_URL)) {
                    $xmlUrl = $nfeData[$campo];
                    Log::info('Focus NFe - URL XML encontrada', ['campo' => $campo, 'url' => $xmlUrl]);
                    break;
                }
            }
            
            // Se encontrou a URL, fazer download direto
            if ($xmlUrl) {
                $xmlResponse = $this->client->get($xmlUrl);
                return $xmlResponse->getBody()->getContents();
            }
            
            // Tentar endpoints alternativos
            try {
                $response = $this->client->get("/v2/nfe/{$ref}.xml");
                return $response->getBody()->getContents();
            } catch (RequestException $e) {
                if ($e->getResponse() && $e->getResponse()->getStatusCode() === 404) {
                    // Tentar endpoint /xml
                    $response = $this->client->get("/v2/nfe/{$ref}/xml");
                    return $response->getBody()->getContents();
                } else {
                    throw $e;
                }
            }
            
        } catch (RequestException $e) {
            Log::error('Focus NFe - Erro ao baixar XML', [
                'ref' => $ref,
                'error' => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null
            ]);
            
            throw new \Exception('Erro ao baixar XML: ' . $e->getMessage());
        }
    }

    /**
     * Montar dados da NFe para API
     */
    private function montarDadosNfe(Nfe $nfe)
    {
        $nfe->load('items', 'company', 'duplicatas');
        
        // Log para debug
        Log::info('Montando dados NFe', [
            'nfe_id' => $nfe->id,
            'local_destino' => $nfe->local_destino,
            'uf_emitente' => 'PA', // Fixo para homologação
            'uf_destinatario' => $nfe->uf_destinatario
        ]);
        
        $dados = [
            'natureza_operacao' => $nfe->natureza_operacao ?: 'Venda para teste de homologacao',
            'data_emissao' => $nfe->data_emissao->format('Y-m-d'),
            'data_entrada_saida' => $nfe->data_emissao->format('Y-m-d'),
            'tipo_documento' => (int)$nfe->tipo_documento ?: 1,
            'finalidade_emissao' => (int)$nfe->finalidade_emissao ?: 1, // Usar valor do formulário
            
            // CORREÇÃO CRÍTICA: Usar idDest em vez de local_destino
            'idDest' => (int)$nfe->local_destino ?: 1, // 1 = Interna, 2 = Interestadual, 3 = Exterior
            
            // Dados do emitente de homologação (CNPJ autorizado)
            'cnpj_emitente' => '61196441000103',
            'nome_emitente' => 'GUABINORTE COMERCIO DE RACAO ANIMAL LTDA',
            'logradouro_emitente' => 'ROD BR 316',
            'numero_emitente' => 'S/N',
            'bairro_emitente' => 'SANTA ROSA',
            'municipio_emitente' => 'BENEVIDES',
            'uf_emitente' => 'PA',
            'cep_emitente' => '68795000',
            'inscricao_estadual_emitente' => '750328711',
            'regime_tributario' => '1', // 1 = Simples Nacional
            
            // Dados do destinatário (usando dados do formulário)
            'nome_destinatario' => $nfe->nome_destinatario ?: 'CLIENTE TESTE HOMOLOGACAO',
            'logradouro_destinatario' => $nfe->logradouro_destinatario ?: 'RUA TESTE',
            'numero_destinatario' => $nfe->numero_destinatario ?: '123',
            'bairro_destinatario' => $nfe->bairro_destinatario ?: 'CENTRO',
            'municipio_destinatario' => $nfe->municipio_destinatario ?: 'SAO PAULO',
            'uf_destinatario' => $nfe->uf_destinatario ?: 'SP',
            'cep_destinatario' => $nfe->cep_destinatario ?: '01000000',
            'pais_destinatario' => 'Brasil',
            
            // CORREÇÃO: Só enviar CPF OU CNPJ, nunca os dois
            'inscricao_estadual_destinatario' => $nfe->ie_destinatario ?: null,
        ];

        // Decidir entre CPF ou CNPJ (nunca os dois)
        if (!empty($nfe->cpf_destinatario)) {
            // Se tem CPF, usa CPF
            $dados['cpf_destinatario'] = $nfe->cpf_destinatario;
            \Log::info('NFe usando CPF do destinatário', ['cpf' => $nfe->cpf_destinatario]);
        } elseif (!empty($nfe->cnpj_destinatario)) {
            // Se tem CNPJ, usa CNPJ  
            $dados['cnpj_destinatario'] = $nfe->cnpj_destinatario;
            \Log::info('NFe usando CNPJ do destinatário', ['cnpj' => $nfe->cnpj_destinatario]);
        } else {
            // Fallback para teste (CPF)
            $dados['cpf_destinatario'] = '11111111111';
            \Log::warning('NFe sem CPF/CNPJ, usando CPF padrão para teste');
        }

        // Continuar com as outras configurações
        $dados = array_merge($dados, [
            // Configurações baseadas no tipo de operação
            'consumidor_final' => (int)$nfe->consumidor_final ?: 1,
            'presenca_comprador' => (int)$nfe->presenca_comprador ?: 1,
            
            // Valores totais
            'valor_frete' => (float)$nfe->valor_frete ?: 0,
            'valor_seguro' => (float)$nfe->valor_seguro ?: 0,
            'valor_total' => (float)$nfe->valor_total ?: 100,
            'valor_produtos' => (float)$nfe->valor_produtos ?: 100,
            'modalidade_frete' => (int)$nfe->modalidade_frete ?: 9,
            
            // Totais de impostos (obrigatório para validação SEFAZ)
            'icms_base_calculo' => 0, // Para Simples Nacional geralmente é 0
            'icms_valor' => 0, // Para Simples Nacional geralmente é 0
            'icms_base_calculo_st' => 0,
            'icms_valor_st' => 0,
            'valor_aproximado_total_tributos' => (float)$nfe->valor_total * 0.40, // Estimativa 40% (obrigatório)
            
            // Observações
            'informacoes_adicionais' => $nfe->observacoes ?: null,
            
            'items' => []
        ]);
        if ((int)$nfe->finalidade_emissao === 4) { // Devolução
            // Para ambiente de homologação, usar uma chave válida de teste
            $dados['documentos_referenciados'] = [
                [
                    'chave_nfe' => '35200714200166000187550010000000046113568957' // Chave de teste válida
                ]
            ];
        }

        // Adicionar itens no formato correto
        foreach ($nfe->items as $index => $item) {
            // MELHORADO: Definir CFOP baseado na comparação de estados
            $ufEmitente = 'PA'; // UF do emitente (em produção, pegar da empresa: $nfe->company->state)
            $ufDestinatario = $nfe->uf_destinatario;
            
            // Lógica inteligente de CFOP baseada na UF e natureza da operação
            $natureza = strtolower($nfe->natureza_operacao ?? '');
            
            if ($ufDestinatario !== $ufEmitente) {
                // Estados diferentes = CFOP interestadual (6xxx)
                if (strpos($natureza, 'produção') !== false || strpos($natureza, 'producao') !== false) {
                    $cfopPadrao = '6101'; // Venda de produção do estabelecimento (interestadual)
                } elseif (strpos($natureza, 'devolução') !== false || strpos($natureza, 'devolucao') !== false) {
                    $cfopPadrao = '6202'; // Devolução de compra para industrialização (interestadual)
                } elseif (strpos($natureza, 'bonificação') !== false || strpos($natureza, 'bonificacao') !== false) {
                    $cfopPadrao = '6910'; // Bonificação (interestadual)
                } else {
                    $cfopPadrao = '6102'; // Venda de mercadoria adquirida/recebida de terceiros (interestadual)
                }
                
                \Log::info("CFOP Interestadual selecionado", [
                    'uf_emitente' => $ufEmitente,
                    'uf_destinatario' => $ufDestinatario,
                    'natureza_operacao' => $natureza,
                    'cfop' => $cfopPadrao
                ]);
            } else {
                // Mesmo estado = CFOP dentro do estado (5xxx)
                if (strpos($natureza, 'produção') !== false || strpos($natureza, 'producao') !== false) {
                    $cfopPadrao = '5101'; // Venda de produção do estabelecimento (dentro do estado)
                } elseif (strpos($natureza, 'devolução') !== false || strpos($natureza, 'devolucao') !== false) {
                    $cfopPadrao = '5202'; // Devolução de compra para industrialização (dentro do estado)
                } elseif (strpos($natureza, 'bonificação') !== false || strpos($natureza, 'bonificacao') !== false) {
                    $cfopPadrao = '5910'; // Bonificação (dentro do estado)
                } else {
                    $cfopPadrao = '5102'; // Venda de mercadoria adquirida/recebida de terceiros (dentro do estado)
                }
                
                \Log::info("CFOP Estadual selecionado", [
                    'uf_emitente' => $ufEmitente,
                    'uf_destinatario' => $ufDestinatario,
                    'natureza_operacao' => $natureza,
                    'cfop' => $cfopPadrao
                ]);
            }
            
            // Calcular valores corretos
            $quantidade = (float)($item->quantidade_comercial ?: 1);
            $valorUnitario = (float)($item->valor_unitario_comercial ?: 100);
            $valorBruto = $quantidade * $valorUnitario;
            
            Log::info('Item da NFe', [
                'item_id' => $item->id,
                'quantidade' => $quantidade,
                'valor_unitario' => $valorUnitario,
                'valor_bruto_calculado' => $valorBruto,
                'valor_bruto_item' => $item->valor_bruto_produtos,
                'dados_enviados' => [
                    'quantidade_comercial' => $quantidade,
                    'valor_unitario_comercial' => $valorUnitario,
                    'valor_bruto' => $valorBruto
                ]
            ]);
            
            $dados['items'][] = [
                'numero_item' => $index + 1,
                'codigo_produto' => $item->codigo_produto ?: 'TESTE001',
                'descricao' => $item->descricao ?: 'PRODUTO PARA TESTE DE HOMOLOGACAO',
                'cfop' => $item->cfop ?: $cfopPadrao,
                'unidade_comercial' => $item->unidade_comercial ?: 'UN',
                'quantidade_comercial' => $quantidade,
                'valor_unitario_comercial' => $valorUnitario,
                'valor_unitario_tributavel' => $valorUnitario,
                'unidade_tributavel' => $item->unidade_comercial ?: 'UN',
                'codigo_ncm' => $item->codigo_ncm ?: '94036000',
                'quantidade_tributavel' => $quantidade,
                'valor_bruto' => $valorBruto, // Valor calculado corretamente
                
                // CORREÇÃO: Para Simples Nacional usar CSOSN com estrutura completa
                'icms_origem' => '0', // String: 0-Nacional, 1-Estrangeira
                'icms_csosn' => '102', // CSOSN 102 = Simples Nacional sem permissão de crédito
                'icms_base_calculo' => 0.00,
                'icms_aliquota' => 0.00,
                'icms_valor' => 0.00,
                
                // Campos específicos para CSOSN 102
                'icms_situacao_tributaria' => '102', // Para garantir compatibilidade
                'icms_modalidade_base_calculo' => '3', // 3 = Valor da operação
                'icms_reducao_base_calculo' => 0.00,
                
                // PIS - Para Simples Nacional
                'pis_situacao_tributaria' => '49', // 49 = Outras operações de saída para Simples Nacional
                'pis_base_calculo' => 0.00,
                'pis_aliquota' => 0.0165,
                'pis_valor' => $valorBruto*0.0165,
                
                // COFINS - Para Simples Nacional  
                'cofins_situacao_tributaria' => '49', // 49 = Outras operações de saída para Simples Nacional
                'cofins_base_calculo' => 0.00,
                'cofins_aliquota' => 0.076,
                'cofins_valor' => $valorBruto*0.076
                
                // IPI REMOVIDO - Para produtos não sujeitos ao IPI, não enviar nenhum campo
            ];
        }

        // Adicionar duplicatas se existirem
        if ($nfe->duplicatas && $nfe->duplicatas->count() > 0) {
            $dados['duplicatas'] = [];
            
            Log::info('Adicionando duplicatas à NFe', [
                'nfe_id' => $nfe->id,
                'quantidade_duplicatas' => $nfe->duplicatas->count()
            ]);
            
            foreach ($nfe->duplicatas as $duplicata) {
                $dados['duplicatas'][] = [
                    'numero_duplicata' => $duplicata->numero,
                    'data_vencimento' => $duplicata->data_vencimento->format('Y-m-d'),
                    'valor' => (float)$duplicata->valor
                ];
                
                Log::info('Duplicata adicionada', [
                    'numero' => $duplicata->numero,
                    'vencimento' => $duplicata->data_vencimento->format('Y-m-d'),
                    'valor' => $duplicata->valor
                ]);
            }
            
            // Para NFe com duplicatas, definir forma de pagamento como "A prazo"
            $dados['forma_pagamento'] = '1'; // 1 = A prazo
            
            Log::info('NFe configurada como pagamento a prazo devido às duplicatas');
        } else {
            Log::info('NFe sem duplicatas', ['nfe_id' => $nfe->id]);
            
            // Para NFe sem duplicatas, definir forma de pagamento como "A vista"
            $dados['forma_pagamento'] = '0'; // 0 = A vista
        }

        return $dados;
    }

    /**
     * Montar dados da NFe para visualização (público)
     */
    public function montarDadosParaVisualizacao(Nfe $nfe)
    {
        return $this->montarDadosNfe($nfe);
    }

    /**
     * Processar resposta da emissão
     */
    private function processarRespostaEmissao(Nfe $nfe, array $resultado)
    {
        if (isset($resultado['status'])) {
            $nfe->status_sefaz = $resultado['status'];
        }

        if (isset($resultado['mensagem_sefaz'])) {
            $nfe->mensagem_sefaz = $resultado['mensagem_sefaz'];
        }

        if (isset($resultado['numero'])) {
            $nfe->numero_nfe = $resultado['numero'];
        }

        if (isset($resultado['chave_nfe'])) {
            $nfe->chave_nfe = $resultado['chave_nfe'];
        }

        // Verificar se foi autorizada
        if (isset($resultado['status']) && $resultado['status'] === 'autorizado') {
            $nfe->status = 'autorizado';
        } elseif (isset($resultado['status']) && in_array($resultado['status'], ['erro_autorizacao', 'rejeitado'])) {
            $nfe->status = 'erro_autorizacao';
        }

        $nfe->save();

        return $resultado;
    }

    /**
     * Obter último número de NFe da API
     */
    public function obterUltimoNumeroNfe($serie = 1)
    {
        try {
            // Consultar informações da empresa na API
            $response = $this->client->get('/v2/empresas');
            $empresas = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Focus NFe - Consulta empresas', ['response' => $empresas]);
            
            // Se retornou array de empresas
            if (is_array($empresas) && !empty($empresas)) {
                foreach ($empresas as $empresa) {
                    // Verificar se tem informações de numeração
                    if (isset($empresa['proximo_numero_nfe'])) {
                        $proximoNumero = intval($empresa['proximo_numero_nfe']);
                        $ultimoNumero = $proximoNumero > 1 ? $proximoNumero - 1 : 1;
                        
                        return [
                            'sucesso' => true,
                            'ultimo_numero' => $ultimoNumero,
                            'proximo_numero' => $proximoNumero,
                            'serie' => $serie,
                            'empresa' => $empresa['cnpj'] ?? 'N/A'
                        ];
                    }
                }
            }
            
            // Método alternativo: tentar consultar NFes recentes
            return $this->obterUltimoNumeroAlternativo($serie);
            
        } catch (RequestException $e) {
            Log::error('Focus NFe - Erro ao obter último número', [
                'error' => $e->getMessage(),
                'serie' => $serie
            ]);
            
            return [
                'sucesso' => false,
                'erro' => 'Erro ao consultar API: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Método alternativo para obter último número
     */
    private function obterUltimoNumeroAlternativo($serie = 1)
    {
        try {
            // Tentar consultar NFes existentes da série
            $response = $this->client->get("/v2/nfe?serie={$serie}&limit=1");
            $nfes = json_decode($response->getBody()->getContents(), true);
            
            if (is_array($nfes) && !empty($nfes)) {
                // Pegar o primeiro resultado (mais recente)
                $ultimaNfe = $nfes[0];
                $ultimoNumero = intval($ultimaNfe['numero'] ?? 0);
                
                return [
                    'sucesso' => true,
                    'ultimo_numero' => $ultimoNumero,
                    'proximo_numero' => $ultimoNumero + 1,
                    'serie' => $serie,
                    'metodo' => 'consulta_nfes'
                ];
            }
            
            // Se não encontrou nenhuma NFe, começar do 1
            return [
                'sucesso' => true,
                'ultimo_numero' => 0,
                'proximo_numero' => 1,
                'serie' => $serie,
                'metodo' => 'primeiro_numero'
            ];
            
        } catch (RequestException $e) {
            return [
                'sucesso' => false,
                'erro' => 'Erro no método alternativo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar configuração
     */
    public function verificarConfiguracao()
    {
        if (empty($this->token)) {
            throw new \Exception('Token da Focus NFe não configurado');
        }

        try {
            // Fazer uma requisição simples para verificar conectividade
            $response = $this->client->get('/v2/empresas');
            return true;
        } catch (RequestException $e) {
            throw new \Exception('Erro na conexão com Focus NFe: ' . $e->getMessage());
        }
    }
}
