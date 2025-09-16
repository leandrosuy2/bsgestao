<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class CnpjController extends Controller
{
    /**
     * Buscar dados de uma empresa pelo CNPJ
     */
    public function search(string $cnpj): JsonResponse
    {
        // Remover caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return response()->json(['message' => 'CNPJ inválido'], 400);
        }

                                                try {
            // Usar a API real da ReceitaWS (sem pontos no CNPJ)
            $url = "https://www.receitaws.com.br/v1/cnpj/{$cnpj}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return response()->json(['message' => 'Erro de conexão: ' . $curlError], 500);
            }

            if ($httpCode !== 200) {
                return response()->json(['message' => 'Erro ao consultar CNPJ (HTTP ' . $httpCode . ')'], 500);
            }

            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json(['message' => 'Erro ao processar resposta da API'], 500);
            }

            if (isset($data['status']) && $data['status'] !== 'OK') {
                return response()->json(['message' => 'CNPJ não encontrado'], 404);
            }

            // Extrair nome do responsável do campo 'nome' (formato: "CNPJ NOME DO RESPONSAVEL")
            $responsibleName = '';
            if (isset($data['nome']) && !empty($data['nome'])) {
                // O nome vem no formato "45.590.374 JAYRO ANDERSON MORAES RODRIGUES"
                // Vamos extrair apenas o nome, removendo o CNPJ
                $nameParts = explode(' ', $data['nome']);
                if (count($nameParts) > 1) {
                    // Remove o primeiro elemento (CNPJ) e junta o resto
                    array_shift($nameParts);
                    $responsibleName = implode(' ', $nameParts);
                }
            }

            // Mapear os dados da API para o formato do sistema
            $companyData = [
                'nome' => $data['nome'] ?? '',
                'email' => $data['email'] ?? '',
                'telefone' => $data['telefone'] ?? '',
                'logradouro' => $data['logradouro'] ?? '',
                'numero' => $data['numero'] ?? '',
                'bairro' => $data['bairro'] ?? '',
                'municipio' => $data['municipio'] ?? '',
                'uf' => $data['uf'] ?? '',
                'cep' => $data['cep'] ?? '',
                'situacao' => $data['situacao'] ?? '',
                'tipo' => $data['tipo'] ?? '',
                'porte' => $data['porte'] ?? '',
                'abertura' => $data['abertura'] ?? '',
                'fantasia' => $data['fantasia'] ?? '',
                'capital_social' => $data['capital_social'] ?? '',
                'natureza_juridica' => $data['natureza_juridica'] ?? '',
                'atividade_principal' => $data['atividade_principal'] ?? [],
                'atividades_secundarias' => $data['atividades_secundarias'] ?? [],
                'simples' => $data['simples'] ?? null,
                'simei' => $data['simei'] ?? null,
                'responsible_name' => $responsibleName,
                'responsible_email' => $data['email'] ?? '', // Usar o mesmo email da empresa
                'responsible_phone' => $data['telefone'] ?? '', // Usar o mesmo telefone da empresa
            ];

            return response()->json($companyData);

                } catch (Exception $e) {
            return response()->json(['message' => 'Erro interno do servidor: ' . $e->getMessage()], 500);
        }
    }
}
