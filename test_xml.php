<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$nfe = App\Models\Nfe::find(52);
if ($nfe) {
    echo "Testing NFe 52...\n";
    
    try {
        $focusService = app(\App\Services\FocusNfeService::class);
        $xml = $focusService->baixarXml($nfe->ref);
        
        echo "XML size: " . strlen($xml) . " bytes\n";
        echo "First 100 chars: " . substr($xml, 0, 100) . "\n";
        echo "Hex dump first 20 bytes: " . bin2hex(substr($xml, 0, 20)) . "\n";
        
        // Check if starts with <?xml
        if (substr($xml, 0, 5) === '<?xml') {
            echo "✓ XML starts with '<?xml'\n";
        } else {
            echo "✗ XML does NOT start with '<?xml'\n";
            echo "Actually starts with: '" . substr($xml, 0, 10) . "'\n";
        }
        
        // Save for inspection
        file_put_contents('debug_xml.xml', $xml);
        echo "XML saved to debug_xml.xml\n";
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "NFe 52 not found\n";
}
