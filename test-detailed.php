<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\FocusNfeService;
use App\Models\Nfe;

echo "=== TESTE DETALHADO DE DOWNLOAD ===\n";

try {
    $nfe = Nfe::find(43);
    $service = new FocusNfeService();
    
    echo "🔄 Fazendo requisição para /v2/nfe/{$nfe->ref}.pdf...\n";
    
    // Usar Guzzle diretamente para ver a resposta completa
    $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://homologacao.focusnfe.com.br',
        'timeout' => 30,
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(config('services.focus_nfe.token') . ':')
        ]
    ]);
    
    $response = $client->get("/v2/nfe/{$nfe->ref}.pdf");
    $content = $response->getBody()->getContents();
    $decoded = json_decode($content, true);
    
    echo "✅ Resposta recebida:\n";
    echo "   Status HTTP: " . $response->getStatusCode() . "\n";
    echo "   Content-Type: " . implode(', ', $response->getHeader('Content-Type')) . "\n";
    echo "   Tamanho: " . strlen($content) . " bytes\n";
    echo "   É JSON: " . (is_array($decoded) ? 'SIM' : 'NÃO') . "\n";
    
    if (is_array($decoded)) {
        echo "\n📄 Conteúdo JSON:\n";
        foreach ($decoded as $key => $value) {
            if (is_string($value) && strlen($value) > 100) {
                $value = substr($value, 0, 100) . '...';
            }
            echo "   {$key}: {$value}\n";
        }
        
        // Procurar URLs
        $urls = [];
        foreach ($decoded as $key => $value) {
            if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                $urls[$key] = $value;
            }
        }
        
        if (!empty($urls)) {
            echo "\n🔗 URLs encontradas:\n";
            foreach ($urls as $key => $url) {
                echo "   {$key}: {$url}\n";
                
                // Tentar baixar o primeiro URL encontrado
                if ($key === array_key_first($urls)) {
                    echo "\n🔄 Testando download da URL: {$url}\n";
                    try {
                        $pdfResponse = $client->get($url);
                        $pdfContent = $pdfResponse->getBody()->getContents();
                        $isPdf = substr($pdfContent, 0, 4) === '%PDF';
                        
                        echo "   Tamanho do PDF: " . strlen($pdfContent) . " bytes\n";
                        echo "   É PDF válido: " . ($isPdf ? 'SIM' : 'NÃO') . "\n";
                        
                        if ($isPdf) {
                            echo "✅ PDF baixado com sucesso!\n";
                        }
                    } catch (Exception $e) {
                        echo "❌ Erro ao baixar URL: " . $e->getMessage() . "\n";
                    }
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DO TESTE ===\n";
