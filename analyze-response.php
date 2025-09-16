<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use GuzzleHttp\Client;

echo "=== ANALISANDO RESPOSTA COMPLETA DA API ===\n";

try {
    $client = new Client([
        'base_uri' => 'https://homologacao.focusnfe.com.br',
        'timeout' => 30,
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(config('services.focus_nfe.token') . ':')
        ]
    ]);
    
    $ref = 'NFE_1755042255_8';
    echo "🔄 Consultando NFe: {$ref}\n\n";
    
    $response = $client->get("/v2/nfe/{$ref}");
    $content = $response->getBody()->getContents();
    $data = json_decode($content, true);
    
    echo "📄 TODOS OS CAMPOS RETORNADOS:\n";
    echo "=====================================\n";
    
    foreach ($data as $key => $value) {
        if (is_string($value)) {
            // Verificar se é URL
            $isUrl = filter_var($value, FILTER_VALIDATE_URL);
            $isPdfUrl = $isUrl && strpos($value, '.pdf') !== false;
            $isXmlUrl = $isUrl && strpos($value, '.xml') !== false;
            
            $indicator = '';
            if ($isPdfUrl) $indicator = ' 📄 [PDF URL]';
            elseif ($isXmlUrl) $indicator = ' 📜 [XML URL]';
            elseif ($isUrl) $indicator = ' 🔗 [URL]';
            
            // Truncar valores muito longos
            $displayValue = strlen($value) > 120 ? substr($value, 0, 120) . '...' : $value;
            
            echo sprintf("%-25s: %s%s\n", $key, $displayValue, $indicator);
        } else {
            echo sprintf("%-25s: %s\n", $key, gettype($value) . (is_array($value) ? ' (' . count($value) . ' items)' : ''));
        }
    }
    
    echo "\n🔍 PROCURANDO URLs DE DOWNLOAD:\n";
    echo "==============================\n";
    
    $urlsEncontradas = [];
    foreach ($data as $key => $value) {
        if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
            $urlsEncontradas[$key] = $value;
            echo "✅ {$key}: {$value}\n";
        }
    }
    
    if (empty($urlsEncontradas)) {
        echo "❌ Nenhuma URL encontrada nos dados retornados.\n";
        echo "💡 A API pode usar outro método para fornecer os arquivos.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
}

echo "\n=== FIM DA ANÁLISE ===\n";
