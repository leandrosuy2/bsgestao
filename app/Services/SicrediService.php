<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class SicrediService
{
    protected $integration;
    protected $token;
    protected $tokenExpiresAt;
    protected $baseUrl;
    protected $authUrl;

    public function __construct($integration = null)
    {
        if ($integration) {
            $this->integration = is_array($integration) ? (object)$integration : $integration;
        } else {
            $this->integration = (object) [
                'client_id' => env('SICREDI_CLIENT_ID', 'b61e4fde-f8de-42c5-a696-63adc48b8e35'),
                'client_secret' => env('SICREDI_CLIENT_SECRET', '394530b9-b0ae-4634-b9ae-5b5c551b4e2e'),
                'x_api_key' => env('SICREDI_X_API_KEY', 'b6661a55-09f0-4ff5-99f3-689768454f51'),
            ];
        }
        
        // URLs de PRODUÇÃO (sem /sb/)
        $this->authUrl = env('SICREDI_AUTH_URL', 'https://api-parceiro.sicredi.com.br/auth/openapi/token');
        $this->baseUrl = env('SICREDI_API_URL', 'https://api-parceiro.sicredi.com.br/cobranca/v1/');
    }

    /**
     * Autentica e armazena token, renovando se necessário
     */
    public function getAccessToken()
    {
        // Se já existe e não expirou, retorna
        if ($this->token && $this->tokenExpiresAt && $this->tokenExpiresAt > now()->addMinute()) {
            return $this->token;
        }
        $payload = [
            'username' => env('SICREDI_USERNAME', '123456789'),
            'password' => env('SICREDI_PASSWORD', 'teste123'),
            'scope' => 'cobranca',
            'grant_type' => 'password',
        ];
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'x-api-key' => $this->integration->x_api_key,
            'context' => 'COBRANCA',
        ];
        Log::info('Sicredi Auth Request', [
            'url' => $this->authUrl,
            'headers' => $headers,
            'payload' => $payload,
        ]);
        // Envia como x-www-form-urlencoded igual ao Postman
        $response = \Http::withHeaders($headers)->asForm()->post($this->authUrl, $payload);
        if ($response->successful() && isset($response['access_token'])) {
            $this->token = $response['access_token'];
            // Calcula expiração (exp em segundos)
            $expiresIn = $response['expires_in'] ?? 3600;
            $this->tokenExpiresAt = now()->addSeconds($expiresIn - 60); // margem de 1 min
            return $this->token;
        }
        Log::error('Sicredi Auth Error', [
            'status' => $response->status(),
            'headers' => $response->headers(),
            'body' => $response->body(),
            'json' => $response->json(),
        ]);
        return null;
    }
 /**
     * Consulta boleto na API Sicredi v1
     */
    public function consultarBoletoV1($codigoBeneficiario, $nossoNumero, $accessToken)
    {
        $url = 'https://api-parceiro.sicredi.com.br/cobranca/boleto/v1/boletos';
        $headers = [
            'x-api-key' => $this->integration->x_api_key,
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
            'cooperativa' => env('SICREDI_COOPERATIVA', '6789'),
            'posto' => env('SICREDI_POSTO', '03'),
        ];
        $params = [
            'codigoBeneficiario' => $codigoBeneficiario,
            'nossoNumero' => $nossoNumero,
        ];
        try {
            $response = \Http::withHeaders($headers)->get($url, $params);
            if ($response->successful()) {
                return $response->json();
            }
            \Log::error('Sicredi Consulta Boleto V1 Error', ['status' => $response->status(), 'body' => $response->body()]);
            return [
                'error' => 'Erro ao consultar boleto',
                'details' => $response->json(),
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        } catch (\Exception $e) {
            \Log::error('Sicredi Consulta Boleto V1 Exception', ['error' => $e->getMessage()]);
            return [
                'error' => 'Exception ao consultar boleto',
                'details' => $e->getMessage(),
            ];
        }
    }
    /**
     * Cria boleto
     */
    public function criarBoleto(array $data)
    {
        // Só aceita access_token manualmente
        // Access token fixo para testes
        $token = "eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJBU0ZSSm1VMTBwaWRpbzFlQ254cXUzV2VwaDFjX2xJWFJDVXk5ZGpTZ0drIn0.eyJleHAiOjE3NTQwMDMzNzUsImlhdCI6MTc1Mzk5OTc3NSwianRpIjoiZmY4NzFjODUtNGYzYy00YTkzLThiMGEtMzc2N2EwM2QxZDM3IiwiaXNzIjoiaHR0cHM6Ly9hdXRoLW9wZW5hcGkudWF0LnNpY3JlZGkuY2xvdWQvcmVhbG1zL29wZW5hcGkiLCJhdWQiOiJhY2NvdW50Iiwic3ViIjoiZjo4YjU0NzRmZi0xZThlLTRiNzYtOWYyYS1iNmExYTIxMzgzZjY6Q09CUkFOQ0E6MTIzNDU2Nzg5IiwidHlwIjoiQmVhcmVyIiwiYXpwIjoib3BlbmFwaS1ndy1zZW5zZWRpYSIsInNlc3Npb25fc3RhdGUiOiJmOTNhMTQwMS00MjlkLTQxNDUtYWUyMy05NTgwNjhlYWYxMmEiLCJhY3IiOiIxIiwicmVhbG1fYWNjZXNzIjp7InJvbGVzIjpbImRlZmF1bHQtcm9sZXMtb3BlbmFwaSIsIm9mZmxpbmVfYWNjZXNzIiwidW1hX2F1dGhvcml6YXRpb24iXX0sInJlc291cmNlX2FjY2VzcyI6eyJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6ImNvYnJhbmNhIGVtYWlsIHByb2ZpbGUiLCJzaWQiOiJmOTNhMTQwMS00MjlkLTQxNDUtYWUyMy05NTgwNjhlYWYxMmEiLCJvcGVuYXBpX3VzZXJuYW1lIjoiMTIzNDU2Nzg5Iiwib3BlbmFwaV9jb250ZXh0IjoiQ09CUkFOQ0EiLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsInByZWZlcnJlZF91c2VybmFtZSI6IjEyMzQ1Njc4OSJ9.DxvmAtNpvqKVmHyxZ1J43ItO9USD0EAbPHrb6Y9mjKx0fuTpCofn_BfS5FBU_yKtJSlJsIpZm18EQdJ9KpBiHsGJHekSHlsHcpmQVKdE7fQ27UHalvEt6cBxYiJXccSvLyPHU3RglK5E0JIIEg3hiRuscYa7q6Nz_azEI8MCT94jXAT4U_Ae5MxPLd3xBfqVsyf7xLafXS7hDRz9vJGDGZMQCswaZJNzL7uD4l6-pmOvin5Fe3q5NhofQFFHRY8CKNXt2_aVMfLcTyP5DG-ULfB10IzEa5d2okMYdqiEpPPs3gr80uucMlSMpw3d7sBqLMLFzZ7Pq1kvOhSyEwKtNg";
        $url = $this->baseUrl . 'boleto/v1/boletos';
        $headers = [
            'x-api-key' => $this->integration->x_api_key,
            'Content-Type' => 'application/json',
            'cooperativa' => env('SICREDI_COOPERATIVA', '6789'),
            'posto' => '03',
            'codigoBeneficiario' => 12345,
        ];
        $response = \Http::withToken($token)
            ->withHeaders($headers)
            ->timeout(30)
            ->post($url, $data);
        if ($response->successful()) {
            return $response->json();
        }
        // Retorna o erro detalhado para o frontend
        Log::error('Sicredi Criar Boleto Error', ['response' => $response->body()]);
        return [
            'error' => 'Erro ao criar boleto',
            'details' => $response->json(),
            'status' => $response->status(),
        ];
    }

    /**
     * Consulta boleto
     */
    public function consultarBoleto($nossoNumero)
    {
        $token = $this->token ?? $this->authenticate();
        if (!$token) return null;
        $url = $this->baseUrl . "/boletos/{$nossoNumero}";
        $response = \Http::withToken($token)
            ->withHeaders([
                'x-api-key' => $this->integration->x_api_key,
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->get($url);
        if ($response->successful()) {
            return $response->json();
        }
        Log::error('Sicredi Consultar Boleto Error', ['response' => $response->body()]);
        return null;
    }

    /**
     * Baixar PDF do boleto
     */
    public function baixarPdf($nossoNumero)
    {
        $token = $this->token ?? $this->authenticate();
        if (!$token) return null;
        $url = $this->baseUrl . "/boletos/{$nossoNumero}/pdf";
        $response = \Http::withToken($token)
            ->withHeaders([
                'x-api-key' => $this->integration->x_api_key,
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->get($url);
        if ($response->successful()) {
            return $response->body(); // PDF binário
        }
        Log::error('Sicredi Baixar PDF Error', ['response' => $response->body()]);
        return null;
    }

    /**
     * Baixa (liquida) boleto
     */
    public function baixarBoleto($nossoNumero, $dataLiquidacao)
    {
        $token = $this->token ?? $this->authenticate();
        if (!$token) return null;
        $url = $this->baseUrl . "/boletos/{$nossoNumero}/baixa";
        $response = \Http::withToken($token)
            ->withHeaders([
                'x-api-key' => $this->integration->x_api_key,
                'Content-Type' => 'application/json',
            ])
            ->timeout(30)
            ->post($url, [
                'dataLiquidacao' => $dataLiquidacao,
            ]);
        if ($response->successful()) {
            return $response->json();
        }
        Log::error('Sicredi Baixar Boleto Error', ['response' => $response->body()]);
        return null;
    }

    /**
     * Criação de boleto Sicredi padronizada (manual oficial)
     */
    public function criarBoletoPadronizado(array $data)
    {
        $url = 'https://api-parceiro.sicredi.com.br/cobranca/boleto/v1/boletos';
        $headers = [
            'x-api-key' => $this->integration->x_api_key,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
            'cooperativa' => env('SICREDI_COOPERATIVA', '6789'),
            'posto' => env('SICREDI_POSTO', '03'),
        ];
        $payload = [
            'beneficiarioFinal' => $data['beneficiarioFinal'],
            'codigoBeneficiario' => $data['codigoBeneficiario'],
            'dataVencimento' => $data['dataVencimento'],
            'especieDocumento' => $data['especieDocumento'],
            'pagador' => $data['pagador'],
            'tipoCobranca' => $data['tipoCobranca'],
            'nossoNumero' => $data['nossoNumero'] ?? null,
            'seuNumero' => $data['seuNumero'],
            'valor' => $data['valor'],
            'tipoDesconto' => $data['tipoDesconto'] ?? null,
            'valorDesconto1' => $data['valorDesconto1'] ?? null,
            'dataDesconto1' => $data['dataDesconto1'] ?? null,
            'valorDesconto2' => $data['valorDesconto2'] ?? null,
            'dataDesconto2' => $data['dataDesconto2'] ?? null,
            'valorDesconto3' => $data['valorDesconto3'] ?? null,
            'dataDesconto3' => $data['dataDesconto3'] ?? null,
            'tipoJuros' => $data['tipoJuros'] ?? null,
            'juros' => $data['juros'] ?? null,
            'multa' => $data['multa'] ?? null,
            'informativos' => $data['informativos'] ?? [],
            'mensagens' => $data['mensagens'] ?? [],
        ];
        if (!empty($data['splitBoleto'])) {
            $payload['splitBoleto'] = $data['splitBoleto'];
        }
        $payload = array_filter($payload, fn($v) => $v !== null);
        try {
            $response = \Http::withHeaders($headers)->post($url, $payload);
            if ($response->successful()) {
                return $response->json();
            }
            $jsonError = null;
            try {
                $jsonError = $response->json();
            } catch (\Exception $e) {
                $jsonError = $response->body();
            }
            \Log::error('Sicredi Boleto Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $jsonError,
                'payload' => $payload,
                'headers' => $headers,
            ]);
            return [
                'error' => 'Erro ao criar boleto',
                'details' => $jsonError,
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            \Log::error('Sicredi Exception', ['msg' => $e->getMessage(), 'payload' => $payload]);
            return [
                'error' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Consulta boleto Sicredi padronizado
     */
    public function consultarBoletoPadronizado($codigoBeneficiario, $nossoNumero)
    {
        $url = env('SICREDI_ENVIRONMENT', 'producao') === 'producao'
            ? "https://api-parceiro.sicredi.com.br/cobranca/boleto/v1/boletos/{$nossoNumero}"
            : "https://api-parceiro.sicredi.com.br/sb/cobranca/boleto/v1/boletos/{$nossoNumero}";
        $headers = [
            'x-api-key' => $this->integration->x_api_key,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
            'cooperativa' => env('SICREDI_COOPERATIVA', '6789'),
            'posto' => env('SICREDI_POSTO', '03'),
            'codigoBeneficiario' => $codigoBeneficiario,
        ];
        try {
            $response = \Http::withHeaders($headers)->get($url);
            if ($response->successful()) {
                return $response->json();
            }
            \Log::error('Sicredi Consulta Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [
                'error' => 'Erro ao consultar boleto',
                'details' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            \Log::error('Sicredi Consulta Exception', ['msg' => $e->getMessage()]);
            return [
                'error' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Lista boletos Sicredi padronizado
     */
    public function listarBoletosPadronizado()
    {
        $url = 'https://api-parceiro.sicredi.com.br/cobranca/boleto/v1/boletos';
        $headers = [
            'x-api-key' => $this->integration->x_api_key,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
            'cooperativa' => env('SICREDI_COOPERATIVA', '6789'),
            'posto' => env('SICREDI_POSTO', '03'),
            'codigoBeneficiario' => env('SICREDI_COD_BENEFICIARIO', '12345'),
        ];
        try {
            $response = \Http::withHeaders($headers)->get($url);
            if ($response->successful()) {
                return $response->json();
            }
            \Log::error('Sicredi Listar Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]); 
            return [
                'error' => 'Erro ao listar boletos',
                'details' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            \Log::error('Sicredi Listar Exception', ['msg' => $e->getMessage()]);
            return [
                'error' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Baixa PDF do boleto Sicredi padronizado
     */
    public function baixarPdfPadronizado($linhaDigitavel)
    {
        $url = env('SICREDI_ENVIRONMENT', 'producao') === 'producao'
            ? 'https://api-parceiro.sicredi.com.br/cobranca/boleto/v1/boletos/pdf'
            : 'https://api-parceiro.sicredi.com.br/sb/cobranca/boleto/v1/boletos/pdf';
        $headers = [
            'x-api-key' => $this->integration->x_api_key,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'Content-Type' => 'application/json',
            'cooperativa' => env('SICREDI_COOPERATIVA', '6789'),
            'posto' => env('SICREDI_POSTO', '03'),
            'codigoBeneficiario' => env('SICREDI_COD_BENEFICIARIO', '12345'),
        ];
        $query = [
            'linhaDigitavel' => $linhaDigitavel,
        ];
        try {
            $response = \Http::withHeaders($headers)->get($url, $query);
            if ($response->successful()) {
                return $response->body(); // PDF binário
            }
            \Log::error('Sicredi PDF Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [
                'error' => 'Erro ao baixar PDF',
                'details' => $response->json(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            \Log::error('Sicredi PDF Exception', ['msg' => $e->getMessage()]);
            return [
                'error' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }
}
