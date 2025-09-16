<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Caminho para o arquivo SQL ordenado
$sqlFile = 'C:\Users\acer\Downloads\dados_ordenados.sql';

if (!file_exists($sqlFile)) {
    echo "Arquivo SQL não encontrado: $sqlFile\n";
    exit(1);
}

echo "Importando dados ordenados...\n";

try {
    // Desabilitar verificações de FK
    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
    echo "✓ Verificações de FK desabilitadas\n";
    
    // Ler e executar arquivo SQL
    $sqlContent = file_get_contents($sqlFile);
    
    // Dividir por comandos (ponto e vírgula + nova linha)
    $commands = array_filter(array_map('trim', explode(";\n", $sqlContent)));
    
    $executed = 0;
    $errors = 0;
    
    foreach ($commands as $command) {
        $command = trim($command);
        
        // Pular comandos de configuração e comentários
        if (empty($command) || 
            strpos($command, '--') === 0 || 
            strpos($command, 'SET ') === 0 ||
            strpos($command, 'START TRANSACTION') === 0 ||
            strpos($command, 'COMMIT') === 0) {
            continue;
        }
        
        // Adicionar ponto e vírgula se não tiver
        if (substr($command, -1) !== ';') {
            $command .= ';';
        }
        
        try {
            DB::statement($command);
            $executed++;
            
            // Extrair nome da tabela para feedback
            if (preg_match('/INSERT INTO `?(\w+)`?/', $command, $matches)) {
                echo "✓ Dados inseridos na tabela: {$matches[1]}\n";
            }
            
        } catch (Exception $e) {
            $errors++;
            echo "✗ Erro: " . $e->getMessage() . "\n";
            
            // Se for erro de duplicata, continuar
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "  (Ignorando duplicata)\n";
                continue;
            }
        }
    }
    
    // Reabilitar verificações de FK
    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    echo "✓ Verificações de FK reabilitadas\n";
    
    echo "\n=== RESUMO ===\n";
    echo "Comandos executados com sucesso: $executed\n";
    echo "Erros encontrados: $errors\n";
    echo "Importação concluída!\n";
    
} catch (Exception $e) {
    echo "Erro fatal: " . $e->getMessage() . "\n";
    
    // Tentar reabilitar FK em caso de erro
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    } catch (Exception $fkError) {
        echo "Erro ao reabilitar FK: " . $fkError->getMessage() . "\n";
    }
}
