<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Caminho para o arquivo SQL
$sqlFile = 'C:\Users\acer\Downloads\u450801758_bsestoque2 (5).sql';

if (!file_exists($sqlFile)) {
    echo "Arquivo SQL não encontrado: $sqlFile\n";
    exit(1);
}

echo "Lendo arquivo SQL...\n";
$sqlContent = file_get_contents($sqlFile);

// Extrair apenas comandos INSERT
$lines = explode("\n", $sqlContent);
$insertCommands = [];
$currentInsert = '';
$inInsert = false;

foreach ($lines as $line) {
    $line = trim($line);
    
    // Pular comentários e linhas vazias
    if (empty($line) || strpos($line, '--') === 0 || strpos($line, '/*') === 0 || strpos($line, '*/') === 0) {
        continue;
    }
    
    // Detectar início de INSERT
    if (strpos($line, 'INSERT INTO') === 0) {
        $inInsert = true;
        $currentInsert = $line;
    } elseif ($inInsert) {
        $currentInsert .= ' ' . $line;
        
        // Detectar final de INSERT (termina com ;)
        if (substr($line, -1) === ';') {
            $insertCommands[] = $currentInsert;
            $currentInsert = '';
            $inInsert = false;
        }
    }
}

echo "Encontrados " . count($insertCommands) . " comandos INSERT\n";

// Executar cada comando INSERT
foreach ($insertCommands as $index => $insertCommand) {
    try {
        echo "Executando INSERT " . ($index + 1) . "...\n";
        
        // Extrair nome da tabela do comando
        preg_match('/INSERT INTO `?(\w+)`?/', $insertCommand, $matches);
        $tableName = $matches[1] ?? 'desconhecida';
        
        echo "Tabela: $tableName\n";
        
        // Executar o comando
        DB::statement($insertCommand);
        echo "✓ Sucesso\n\n";
        
    } catch (Exception $e) {
        echo "✗ Erro na tabela $tableName: " . $e->getMessage() . "\n\n";
        
        // Se for erro de duplicata, continuar
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            echo "Ignorando duplicata e continuando...\n\n";
            continue;
        }
        
        // Para outros erros, parar
        echo "Parando execução devido ao erro.\n";
        break;
    }
}

echo "Importação concluída!\n";
