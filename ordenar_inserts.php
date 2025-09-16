<?php

// Lê o arquivo SQL de dados
$inputFile = 'C:\Users\acer\Downloads\dados_apenas.sql';
$outputFile = 'C:\Users\acer\Downloads\dados_ordenados.sql';

if (!file_exists($inputFile)) {
    echo "Arquivo não encontrado: $inputFile\n";
    exit(1);
}

echo "Ordenando comandos INSERT...\n";
$content = file_get_contents($inputFile);

// Define a ordem correta para inserção (tabelas pai primeiro)
$tableOrder = [
    'companies',
    'users', 
    'roles',
    'permissions',
    'employees',
    'categories',
    'products',
    'suppliers',
    'customers',
    'cache',
    'cache_locks',
    'personal_access_tokens',
    'benefits',
    'payables',
    'receivables',
    'time_clocks',
    'payrolls',
    'vacations',
    'leaves',
    'payslips',
    'stock_movements',
    'cash_registers',
    'cash_movements',
    'sales',
    'sale_items',
    'sale_payments',
    'quotes',
    'quote_items',
    'delivery_receipts',
    'delivery_receipt_items',
    'romaneios',
    'romaneio_items',
    'boletos',
    'nfe',
    'nfe_items',
    'user_payment_integrations',
    'role_permission',
    'user_role',
    'jobs',
    'job_batches',
    'failed_jobs'
];

// Extrai os INSERTs por tabela
$insertsByTable = [];
$lines = explode("\n", $content);
$currentInsert = '';
$currentTable = '';
$inInsert = false;

foreach ($lines as $line) {
    $trimmedLine = trim($line);
    
    // Detectar início de INSERT
    if (strpos($trimmedLine, 'INSERT INTO') === 0) {
        // Extrair nome da tabela
        preg_match('/INSERT INTO `?(\w+)`?/', $trimmedLine, $matches);
        $currentTable = $matches[1] ?? 'unknown';
        $inInsert = true;
        $currentInsert = $line;
        
        // Se termina na mesma linha
        if (substr($trimmedLine, -1) === ';') {
            if (!isset($insertsByTable[$currentTable])) {
                $insertsByTable[$currentTable] = [];
            }
            $insertsByTable[$currentTable][] = $currentInsert;
            $inInsert = false;
            $currentInsert = '';
        }
        continue;
    }
    
    // Continuar INSERT em múltiplas linhas
    if ($inInsert) {
        $currentInsert .= "\n" . $line;
        
        // Detectar fim de INSERT
        if (substr($trimmedLine, -1) === ';') {
            if (!isset($insertsByTable[$currentTable])) {
                $insertsByTable[$currentTable] = [];
            }
            $insertsByTable[$currentTable][] = $currentInsert;
            $inInsert = false;
            $currentInsert = '';
        }
    }
}

// Monta o arquivo de saída ordenado
$outputLines = [];

// Cabeçalho
$outputLines[] = '-- Arquivo de dados ordenado para evitar erros de Foreign Key';
$outputLines[] = '-- Gerado automaticamente em ' . date('Y-m-d H:i:s');
$outputLines[] = '';
$outputLines[] = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";';
$outputLines[] = 'START TRANSACTION;';
$outputLines[] = 'SET time_zone = "+00:00";';
$outputLines[] = '';
$outputLines[] = '-- Desabilita verificações de chave estrangeira temporariamente';
$outputLines[] = 'SET FOREIGN_KEY_CHECKS = 0;';
$outputLines[] = '';

// Inserir dados na ordem correta
foreach ($tableOrder as $table) {
    if (isset($insertsByTable[$table])) {
        $outputLines[] = "-- Dados para tabela `$table`";
        foreach ($insertsByTable[$table] as $insert) {
            // Modificar INSERT para INSERT IGNORE para evitar duplicatas
            $modifiedInsert = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert);
            $outputLines[] = $modifiedInsert;
        }
        $outputLines[] = '';
        unset($insertsByTable[$table]);
    }
}

// Inserir tabelas restantes (que não estavam na ordem definida)
foreach ($insertsByTable as $table => $inserts) {
    $outputLines[] = "-- Dados para tabela `$table` (ordem não definida)";
    foreach ($inserts as $insert) {
        // Modificar INSERT para INSERT IGNORE para evitar duplicatas
        $modifiedInsert = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert);
        $outputLines[] = $modifiedInsert;
    }
    $outputLines[] = '';
}

// Finalização
$outputLines[] = '-- Reabilita verificações de chave estrangeira';
$outputLines[] = 'SET FOREIGN_KEY_CHECKS = 1;';
$outputLines[] = '';
$outputLines[] = 'COMMIT;';

// Salva o arquivo
$output = implode("\n", $outputLines);
file_put_contents($outputFile, $output);

echo "Arquivo ordenado com sucesso!\n";
echo "Arquivo original: $inputFile\n";
echo "Arquivo ordenado: $outputFile\n";
echo "Tabelas processadas: " . count($insertsByTable) . "\n";
