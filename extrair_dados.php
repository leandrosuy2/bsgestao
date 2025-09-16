<?php

// Lê o arquivo SQL original
$inputFile = 'C:\Users\acer\Downloads\u450801758_bsestoque2 (4).sql';
$outputFile = 'C:\Users\acer\Downloads\dados_apenas.sql';

if (!file_exists($inputFile)) {
    echo "Arquivo não encontrado: $inputFile\n";
    exit(1);
}

echo "Processando arquivo SQL...\n";
$content = file_get_contents($inputFile);
$lines = explode("\n", $content);

$outputLines = [];
$inCreateTable = false;
$inInsert = false;
$currentInsert = '';

// Adiciona cabeçalho
$outputLines[] = '-- Arquivo contendo apenas dados (INSERT)';
$outputLines[] = '-- Gerado automaticamente em ' . date('Y-m-d H:i:s');
$outputLines[] = '';
$outputLines[] = 'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";';
$outputLines[] = 'START TRANSACTION;';
$outputLines[] = 'SET time_zone = "+00:00";';
$outputLines[] = '';

foreach ($lines as $line) {
    $trimmedLine = trim($line);
    
    // Pular linhas vazias e comentários do phpMyAdmin
    if (empty($trimmedLine) || 
        strpos($trimmedLine, '-- phpMyAdmin') === 0 ||
        strpos($trimmedLine, '-- version') === 0 ||
        strpos($trimmedLine, '-- https://') === 0 ||
        strpos($trimmedLine, '-- Host:') === 0 ||
        strpos($trimmedLine, '-- Tempo de') === 0 ||
        strpos($trimmedLine, '-- Versão') === 0 ||
        strpos($trimmedLine, '/*!40101') === 0) {
        continue;
    }
    
    // Detectar início de CREATE TABLE
    if (strpos($trimmedLine, 'CREATE TABLE') === 0 || 
        strpos($trimmedLine, 'DROP TABLE') === 0) {
        $inCreateTable = true;
        continue;
    }
    
    // Detectar fim de CREATE TABLE
    if ($inCreateTable && (strpos($trimmedLine, ') ENGINE=') === 0 || 
                           strpos($trimmedLine, 'ENGINE=') !== false ||
                           $trimmedLine === ');')) {
        $inCreateTable = false;
        continue;
    }
    
    // Pular linhas dentro de CREATE TABLE
    if ($inCreateTable) {
        continue;
    }
    
    // Pular comentários de estrutura
    if (strpos($trimmedLine, '-- Estrutura para tabela') === 0 ||
        strpos($trimmedLine, '-- --------------------------------------------------------') === 0) {
        continue;
    }
    
    // Detectar início de INSERT
    if (strpos($trimmedLine, 'INSERT INTO') === 0) {
        $inInsert = true;
        $currentInsert = $line;
        
        // Se termina na mesma linha
        if (substr($trimmedLine, -1) === ';') {
            $outputLines[] = $currentInsert;
            $outputLines[] = '';
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
            $outputLines[] = $currentInsert;
            $outputLines[] = '';
            $inInsert = false;
            $currentInsert = '';
        }
        continue;
    }
    
    // Manter comentários de dados
    if (strpos($trimmedLine, '-- Despejando dados') === 0) {
        $outputLines[] = $line;
        $outputLines[] = '';
        continue;
    }
    
    // Manter comandos de configuração específicos
    if (strpos($trimmedLine, 'ALTER TABLE') === 0 ||
        strpos($trimmedLine, 'ADD PRIMARY KEY') === 0 ||
        strpos($trimmedLine, 'ADD UNIQUE KEY') === 0 ||
        strpos($trimmedLine, 'ADD KEY') === 0 ||
        strpos($trimmedLine, 'MODIFY') === 0 ||
        strpos($trimmedLine, 'AUTO_INCREMENT=') === 0 ||
        strpos($trimmedLine, 'ADD CONSTRAINT') === 0) {
        // Pular comandos de alteração de estrutura
        continue;
    }
    
    // Manter comandos COMMIT
    if ($trimmedLine === 'COMMIT;') {
        $outputLines[] = $line;
        continue;
    }
}

// Adiciona COMMIT no final se não houver
if (end($outputLines) !== 'COMMIT;') {
    $outputLines[] = 'COMMIT;';
}

// Salva o arquivo
$output = implode("\n", $outputLines);
file_put_contents($outputFile, $output);

echo "Arquivo processado com sucesso!\n";
echo "Arquivo original: $inputFile\n";
echo "Arquivo de dados: $outputFile\n";
echo "Total de linhas processadas: " . count($lines) . "\n";
echo "Total de linhas de dados: " . count($outputLines) . "\n";
