<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SicrediService;
use App\Models\Boleto;

class VendaBoletoController extends Controller
{
    public function index()
    {
        return view('venda-boleto');
    }

    public function gerar(Request $request)
    {
        $informativos = array_filter(array_map(function($i) {
            $i = trim($i);
            return mb_substr($i, 0, 80);
        }, preg_split('/\r?\n/', $request->input('instrucoes', ''))));
        if (count($informativos) < 1) {
            $informativos = ['Pagamento até o vencimento.'];
        }
        if (count($informativos) > 5) {
            $informativos = array_slice($informativos, 0, 5);
        }
        $mensagens = array_filter(array_map(function($m) {
            $m = trim($m);
            return mb_substr($m, 0, 80);
        }, preg_split('/\r?\n/', $request->input('mensagens', ''))));
        if (count($mensagens) < 1) {
            $mensagens = ['Obrigado pela preferência.'];
        }
        if (count($mensagens) > 4) {
            $mensagens = array_slice($mensagens, 0, 4);
        }
        $data = [
            'beneficiarioFinal' => [
                'cep' => env('SICREDI_BENEFICIARIO_CEP', '91250000'),
                'cidade' => env('SICREDI_BENEFICIARIO_CIDADE', 'PORTO ALEGRE'),
                'documento' => env('SICREDI_BENEFICIARIO_DOCUMENTO', '65613259585'),
                'logradouro' => env('SICREDI_BENEFICIARIO_LOGRADOURO', 'RUA DOUTOR VARGAS NETO 180'),
                'nome' => env('SICREDI_BENEFICIARIO_NOME', 'TESTE FAKE'),
                'numeroEndereco' => env('SICREDI_BENEFICIARIO_NUMERO', 119),
                'tipoPessoa' => env('SICREDI_BENEFICIARIO_TIPO', 'PESSOA_FISICA'),
                'uf' => env('SICREDI_BENEFICIARIO_UF', 'RS'),
            ],
            'codigoBeneficiario' => env('SICREDI_COD_BENEFICIARIO', '12345'),
            'dataVencimento' => $request->input('dataVencimento'),
            'especieDocumento' => 'DUPLICATA_MERCANTIL_INDICACAO',
            'pagador' => [
                'cep' => str_pad(preg_replace('/\D/', '', $request->input('cliente_cep')), 8, '0', STR_PAD_LEFT),
                'cidade' => $request->input('cliente_cidade'),
                'documento' => $request->input('cliente_documento'),
                'nome' => $request->input('cliente_nome'),
                'tipoPessoa' => strlen(preg_replace('/\D/','',$request->input('cliente_documento'))) === 11 ? 'PESSOA_FISICA' : 'PESSOA_JURIDICA',
                'endereco' => $request->input('cliente_endereco'),
                'uf' => $request->input('cliente_uf'),
            ],
            'tipoCobranca' => 'HIBRIDO',
            'seuNumero' => $request->input('seuNumero'),
            'valor' => $request->input('valor'),
            'informativos' => $informativos,
            'mensagens' => $mensagens,
        ];
        $service = new SicrediService();
        $result = $service->criarBoletoPadronizado($data);
        $pdfLink = null;
        
        if (is_array($result) && isset($result['linhaDigitavel'])) {
            $pdf = $service->baixarPdfPadronizado($result['linhaDigitavel']);
            if (is_string($pdf) && strlen($pdf) > 1000) {
                $pdfFile = storage_path('app/public/boleto_' . ($result['nossoNumero'] ?? time()) . '.pdf');
                file_put_contents($pdfFile, $pdf);
                $pdfLink = asset('storage/' . basename($pdfFile));
            }

            // Salvar boleto na database
            Boleto::create([
                'txid' => $result['txid'] ?? '',
                'qr_code' => $result['qrCode'] ?? '',
                'linha_digitavel' => $result['linhaDigitavel'] ?? '',
                'codigo_barras' => $result['codigoBarras'] ?? '',
                'cooperativa' => $result['cooperativa'] ?? '',
                'posto' => $result['posto'] ?? '',
                'nosso_numero' => $result['nossoNumero'] ?? '',
                'cliente_nome' => $request->input('cliente_nome'),
                'cliente_documento' => $request->input('cliente_documento'),
                'cliente_endereco' => $request->input('cliente_endereco'),
                'cliente_cidade' => $request->input('cliente_cidade'),
                'cliente_uf' => $request->input('cliente_uf'),
                'cliente_cep' => str_pad(preg_replace('/\D/', '', $request->input('cliente_cep')), 8, '0', STR_PAD_LEFT),
                'valor' => $request->input('valor'),
                'data_vencimento' => $request->input('dataVencimento'),
                'seu_numero' => $request->input('seuNumero'),
                'instrucoes' => $request->input('instrucoes'),
                'status' => 'gerado',
                'pdf_path' => $pdfLink
            ]);
        }
        
        return back()->with([
            'boleto_result' => json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE),
            'boleto_pdf' => $pdfLink
        ]);
    }

    public function pesquisarBoletos(Request $request)
    {
        $query = Boleto::orderBy('created_at', 'desc');
        
        // Filtro por cliente
        if ($request->filled('cliente')) {
            $query->where('cliente_nome', 'like', '%' . $request->cliente . '%');
        }
        
        // Filtro por documento
        if ($request->filled('documento')) {
            $query->where('cliente_documento', 'like', '%' . $request->documento . '%');
        }
        
        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $boletos = $query->get();
        return view('pesquisar-boletos', compact('boletos'));
    }

    public function listarBoletosApi()
    {
        $result = (new SicrediService())->listarBoletosPadronizado();
        return back()->with('boletos_lista', json_encode($result, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }
}
