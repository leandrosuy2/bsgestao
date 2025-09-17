<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Controle de Estoque</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #1f2937;
        }
        
        .header p {
            font-size: 12px;
            color: #6b7280;
            margin: 3px 0;
        }
        
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        
        .summary-item:first-child {
            border-top-left-radius: 6px;
            border-bottom-left-radius: 6px;
        }
        
        .summary-item:last-child {
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        
        .summary-item h3 {
            font-size: 9px;
            font-weight: 600;
            color: #6b7280;
            margin: 0 0 3px 0;
            text-transform: uppercase;
        }
        
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin: 0;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section h2 {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 10px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            padding: 6px 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        th {
            background-color: #f3f4f6;
            font-weight: 600;
            font-size: 9px;
            color: #374151;
            text-transform: uppercase;
        }
        
        td {
            font-size: 9px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .text-sm {
            font-size: 8px;
        }
        
        .text-lg {
            font-size: 12px;
        }
        
        .bg-gray-50 {
            background-color: #f9fafb;
        }
        
        .border {
            border: 1px solid #e5e7eb;
        }
        
        .rounded {
            border-radius: 4px;
        }
        
        .p-3 {
            padding: 12px;
        }
        
        .mb-3 {
            margin-bottom: 12px;
        }
        
        .mt-3 {
            margin-top: 12px;
        }
        
        .no-data {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 15px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }
        
        .status-normal { background-color: #dcfce7; color: #166534; }
        .status-low { background-color: #fef3c7; color: #92400e; }
        .status-high { background-color: #dbeafe; color: #1e40af; }
        .status-zero { background-color: #f3f4f6; color: #374151; }
        
        .positive { color: #059669; }
        .negative { color: #dc2626; }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h1>Relatório de Controle de Estoque</h1>
        <p><strong>Empresa:</strong> {{ $user->company->name ?? 'N/A' }}</p>
        @if($category)
            <p><strong>Categoria:</strong> {{ $category->name }}</p>
        @endif
        <p><strong>Gerado em:</strong> {{ $generatedAt->format('d/m/Y H:i') }}</p>
        <p><strong>Incluindo estoque zero:</strong> {{ $showZeroStock ? 'Sim' : 'Não' }}</p>
    </div>

    <!-- Resumo Executivo -->
    <div class="summary">
        <div class="summary-item">
            <h3>Total de Produtos</h3>
            <p class="value">{{ number_format($stats['total_products']) }}</p>
        </div>
        <div class="summary-item">
            <h3>Estoque Físico Total</h3>
            <p class="value">{{ number_format($stats['total_physical_stock']) }}</p>
        </div>
        <div class="summary-item">
            <h3>Divergências</h3>
            <p class="value">{{ number_format($stats['products_with_difference']) }}</p>
        </div>
        <div class="summary-item">
            <h3>Valor Total</h3>
            <p class="value">R$ {{ number_format($stats['total_value'], 2, ',', '.') }}</p>
        </div>
    </div>

    <!-- Estatísticas Adicionais -->
    <div class="section">
        <h2>Estatísticas do Estoque</h2>
        <div class="bg-gray-50 border rounded p-3">
            <div style="display: table; width: 100%;">
                <div style="display: table-cell; width: 33%;">
                    <strong>Produtos com estoque baixo:</strong> {{ $stats['low_stock_products'] }}<br>
                    <strong>Produtos sem estoque:</strong> {{ $stats['zero_stock_products'] }}
                </div>
                <div style="display: table-cell; width: 33%;">
                    <strong>Estoque Físico:</strong> {{ number_format($stats['total_physical_stock']) }}<br>
                    <strong>Estoque Virtual:</strong> {{ number_format($stats['total_virtual_stock']) }}
                </div>
                <div style="display: table-cell; width: 33%;">
                    <strong>Diferença Total:</strong> <span class="{{ $stats['total_difference'] >= 0 ? 'positive' : 'negative' }}">{{ $stats['total_difference'] >= 0 ? '+' : '' }}{{ number_format($stats['total_difference']) }}</span><br>
                    <strong>Precisão:</strong> {{ number_format($stats['accuracy_percentage'], 1) }}%
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Produtos -->
    <div class="section">
        <h2>Detalhamento por Produto</h2>
        @if(count($products) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th class="text-center">Estoque Físico</th>
                        <th class="text-center">Estoque Virtual</th>
                        <th class="text-center">Diferença</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $item)
                        <tr>
                            <td>
                                <div class="font-bold">{{ $item['product']->name }}</div>
                                <div class="text-sm">{{ $item['product']->internal_code }}</div>
                            </td>
                            <td>{{ $item['category']->name ?? 'N/A' }}</td>
                            <td class="text-center font-bold positive">{{ number_format($item['physical_stock']) }}</td>
                            <td class="text-center font-bold">{{ number_format($item['virtual_stock']) }}</td>
                            <td class="text-center font-bold {{ $item['difference'] >= 0 ? 'positive' : 'negative' }}">
                                {{ $item['difference'] >= 0 ? '+' : '' }}{{ number_format($item['difference']) }}
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClasses = [
                                        'normal' => 'status-normal',
                                        'low' => 'status-low',
                                        'high' => 'status-high',
                                        'zero' => 'status-zero'
                                    ];
                                    $statusLabels = [
                                        'normal' => 'Normal',
                                        'low' => 'Baixo',
                                        'high' => 'Alto',
                                        'zero' => 'Zero'
                                    ];
                                @endphp
                                <span class="{{ $statusClasses[$item['status']] }}" style="padding: 2px 6px; border-radius: 3px; font-size: 8px;">
                                    {{ $statusLabels[$item['status']] }}
                                </span>
                            </td>
                            <td class="text-right font-bold">R$ {{ number_format($item['stock_value'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Nenhum produto encontrado com os filtros aplicados
            </div>
        @endif
    </div>

    <!-- Top Divergências -->
    @if(count($products) > 0)
    <div class="section">
        <h2>Top 10 Maiores Divergências</h2>
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 10px;">
                @foreach(array_slice($products, 0, 5) as $item)
                    @if($item['difference'] != 0)
                    <div class="border rounded p-3 mb-3">
                        <div class="font-bold">{{ $item['product']->name }}</div>
                        <div class="text-sm">{{ $item['category']->name ?? 'N/A' }}</div>
                        <div class="text-right">
                            <span class="font-bold {{ $item['difference'] >= 0 ? 'positive' : 'negative' }}">
                                {{ $item['difference'] >= 0 ? '+' : '' }}{{ number_format($item['difference']) }}
                            </span>
                            <div class="text-sm">
                                Físico: {{ $item['physical_stock'] }} | Virtual: {{ $item['virtual_stock'] }}
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            <div style="display: table-cell; width: 50%; vertical-align: top; padding-left: 10px;">
                @foreach(array_slice($products, 5, 5) as $item)
                    @if($item['difference'] != 0)
                    <div class="border rounded p-3 mb-3">
                        <div class="font-bold">{{ $item['product']->name }}</div>
                        <div class="text-sm">{{ $item['category']->name ?? 'N/A' }}</div>
                        <div class="text-right">
                            <span class="font-bold {{ $item['difference'] >= 0 ? 'positive' : 'negative' }}">
                                {{ $item['difference'] >= 0 ? '+' : '' }}{{ number_format($item['difference']) }}
                            </span>
                            <div class="text-sm">
                                Físico: {{ $item['physical_stock'] }} | Virtual: {{ $item['virtual_stock'] }}
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Resumo Final -->
    <div class="section">
        <h2>Resumo e Recomendações</h2>
        <div class="bg-gray-50 border rounded p-3">
            <p><strong>Total de produtos analisados:</strong> {{ number_format($stats['total_products']) }}</p>
            <p><strong>Produtos com divergências:</strong> {{ number_format($stats['products_with_difference']) }} ({{ number_format(($stats['products_with_difference'] / $stats['total_products']) * 100, 1) }}%)</p>
            <p><strong>Valor total do estoque físico:</strong> R$ {{ number_format($stats['total_value'], 2, ',', '.') }}</p>
            
            @if($stats['products_with_difference'] > 0)
                <p class="mt-3"><strong>⚠️ Recomendação:</strong> Realizar inventário físico para corrigir as divergências encontradas.</p>
            @endif
            
            @if($stats['low_stock_products'] > 0)
                <p><strong>⚠️ Atenção:</strong> {{ $stats['low_stock_products'] }} produtos com estoque baixo precisam de reposição.</p>
            @endif
            
            @if($stats['zero_stock_products'] > 0)
                <p><strong>ℹ️ Informação:</strong> {{ $stats['zero_stock_products'] }} produtos estão sem estoque.</p>
            @endif
        </div>
    </div>

    <!-- Rodapé -->
    <div class="footer">
        <p>Relatório gerado automaticamente pelo sistema BSEstoque em {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este relatório contém informações confidenciais e deve ser tratado com segurança</p>
    </div>
</body>
</html>
