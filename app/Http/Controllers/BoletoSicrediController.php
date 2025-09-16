<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserPaymentIntegration;
use App\Services\SicrediService;

class BoletoSicrediController extends Controller
   
{
    public function index()
    {
        $users = User::with('paymentIntegration')->get();
        return view('boletos.sicredi', compact('users'));
    }

    /**
     * Criação de boleto Sicredi (padronizado)
     */
    public function criar(Request $request)
    {
        $data = $request->validate([
            'beneficiarioFinal' => 'required|array',
            'codigoBeneficiario' => 'required|string',
            'dataVencimento' => 'required|date',
            'especieDocumento' => 'required|string',
            'pagador' => 'required|array',
            'tipoCobranca' => 'required|string',
            'seuNumero' => 'required|string',
            'valor' => 'required|numeric|min:0.01',
            // Split e descontos opcionais
            'splitBoleto' => 'nullable|array',
            'tipoDesconto' => 'nullable|string',
            'valorDesconto1' => 'nullable|numeric',
            'dataDesconto1' => 'nullable|date',
            'valorDesconto2' => 'nullable|numeric',
            'dataDesconto2' => 'nullable|date',
            'valorDesconto3' => 'nullable|numeric',
            'dataDesconto3' => 'nullable|date',
            'tipoJuros' => 'nullable|string',
            'juros' => 'nullable|numeric',
            'multa' => 'nullable|numeric',
            'informativos' => 'nullable|array',
            'mensagens' => 'nullable|array',
        ]);
        $accessToken = $request->input('access_token');
        $service = new \App\Services\SicrediService();
        $result = $service->criarBoletoPadronizado($data, $accessToken);
        return response()->json($result);
    }
 /**
     * Consulta boleto Sicredi v1 via GET
     */
    /**
     * Consulta boleto Sicredi (padronizado)
     */
    public function consultar(Request $request)
    {
        $codigoBeneficiario = $request->input('codigoBeneficiario', env('SICREDI_COD_BENEFICIARIO', '12345'));
        $nossoNumero = $request->input('nossoNumero');
        $accessToken = $request->input('access_token');
        $service = new \App\Services\SicrediService();
        $result = $service->consultarBoletoPadronizado($codigoBeneficiario, $nossoNumero, $accessToken);
        return response()->json($result);
    }
 /**
     * Consulta todos os boletos Sicredi
     */
    /**
     * Lista boletos Sicredi (padronizado)
     */
    public function listar(Request $request)
    {
        $accessToken = $request->input('access_token');
        $service = new \App\Services\SicrediService();
        $result = $service->listarBoletosPadronizado($accessToken);
        return response()->json($result);
    }
    /**
     * Baixar PDF do boleto Sicredi (padronizado)
     */
    public function baixarPdf(Request $request)
    {
        $linhaDigitavel = $request->input('linhaDigitavel');
        $accessToken = $request->input('access_token');
        $service = new \App\Services\SicrediService();
        $result = $service->baixarPdfPadronizado($linhaDigitavel, $accessToken);
        if (is_string($result)) {
            // PDF binário
            return response($result, 200)->header('Content-Type', 'application/pdf');
        }
        return response()->json($result);
    }

}
