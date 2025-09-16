<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SicrediService;

class SicrediTesteController extends Controller
{
    public function index()
    {
        return view('sicredi-teste');
    }

    public function criar(Request $request)
    {
        $payload = json_decode($request->input('payload'), true);
        $result = (new SicrediService())->criarBoletoPadronizado($payload);
        return back()->with('result', $result);
    }

    public function consultar(Request $request)
    {
        $codigoBeneficiario = $request->input('codigoBeneficiario');
        $nossoNumero = $request->input('nossoNumero');
        $result = (new SicrediService())->consultarBoletoPadronizado($codigoBeneficiario, $nossoNumero);
        return back()->with('result', $result);
    }

    public function listar(Request $request)
    {
        $result = (new SicrediService())->listarBoletosPadronizado();
        return back()->with('result', $result);
    }

    public function pdf(Request $request)
    {
        $linhaDigitavel = $request->input('linhaDigitavel');
        $result = (new SicrediService())->baixarPdfPadronizado($linhaDigitavel);
        if (is_string($result)) {
            return response($result, 200)->header('Content-Type', 'application/pdf');
        }
        return back()->with('result', $result);
    }
}
