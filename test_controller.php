<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing direct controller method...\n";

$nfe = App\Models\Nfe::find(52);
if ($nfe) {
    $controller = new App\Http\Controllers\NfeController(app(\App\Services\FocusNfeService::class));
    
    echo "About to call controller->xml method...\n";
    
    // Start output buffering to capture any unwanted output
    ob_start();
    
    try {
        $response = $controller->xml($nfe);
        $capturedOutput = ob_get_contents();
        ob_end_clean();
        
        echo "Captured output length: " . strlen($capturedOutput) . "\n";
        if (strlen($capturedOutput) > 0) {
            echo "Captured output (first 100 chars): '" . substr($capturedOutput, 0, 100) . "'\n";
            echo "Captured output hex: " . bin2hex(substr($capturedOutput, 0, 50)) . "\n";
        } else {
            echo "No unwanted output captured ✓\n";
        }
        
        $content = $response->getContent();
        echo "Response content size: " . strlen($content) . "\n";
        echo "Response starts with: '" . substr($content, 0, 20) . "'\n";
        
        if (substr($content, 0, 5) === '<?xml') {
            echo "✓ Response starts correctly with <?xml\n";
        } else {
            echo "✗ Response does NOT start with <?xml\n";
            echo "Hex dump first 20 bytes: " . bin2hex(substr($content, 0, 20)) . "\n";
        }
        
        // Check headers
        $headers = $response->headers->all();
        echo "Content-Type: " . ($headers['content-type'][0] ?? 'not set') . "\n";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "NFe 52 not found\n";
}
