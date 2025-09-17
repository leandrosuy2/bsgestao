<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Vendas - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 10px 0;
            color: #1f2937;
        }
        
        .header p {
            font-size: 14px;
            color: #6b7280;
            margin: 5px 0;
        }
        
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .summary-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        
        .summary-item:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        
        .summary-item:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        
        .summary-item h3 {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 0;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section h2 {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        th {
            background-color: #f3f4f6;
            font-weight: 600;
            font-size: 11px;
            color: #374151;
            text-transform: uppercase;
        }
        
        td {
            font-size: 11px;
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
            font-size: 10px;
        }
        
        .text-lg {
            font-size: 14px;
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
        
        .p-4 {
            padding: 16px;
        }
        
        .mb-4 {
            margin-bottom: 16px;
        }
        
        .mt-4 {
            margin-top: 16px;
        }
        
        .no-data {
            text-align: center;
            color: #6b7280;
            font-style: italic;
            padding: 20px;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h1>Relatório de Vendas</h1>
        <p><strong>Período:</strong> {{ $periods['label'] }}</p>
        <p><strong>Usuário:</strong> {{ $user->name }} ({{ $user->email }})</p>
        <p><strong>Empresa:</strong> {{ $user->company->name ?? 'N/A' }}</p>
        <p><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Resumo Executivo -->
    <div class="summary">
        <div class="summary-item">
            <h3>Total de Vendas</h3>
            <p class="value">R$ {{ number_format($totalSales, 2, ',', '.') }}</p>
        </div>
        <div class="summary-item">
            <h3>Número de Vendas</h3>
            <p class="value">{{ number_format($totalSalesCount) }}</p>
        </div>
        <div class="summary-item">
            <h3>Ticket Médio</h3>
            <p class="value">R$ {{ number_format($averageTicket, 2, ',', '.') }}</p>
        </div>
        <div class="summary-item">
            <h3>Clientes Atendidos</h3>
            <p class="value">{{ $salesByCustomer->count() }}</p>
        </div>
    </div>

    <!-- Vendas por Cliente -->
    <div class="section">
        <h2>Vendas por Cliente</h2>
        @if($salesByCustomer->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th class="text-right">Total de Vendas</th>
                        <th class="text-center">Número de Vendas</th>
                        <th class="text-right">Ticket Médio</th>
                        <th class="text-center">% do Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salesByCustomer as $customer)
                        <tr>
                            <td>{{ $customer['customer'] }}</td>
                            <td class="text-right font-bold">R$ {{ number_format($customer['total'], 2, ',', '.') }}</td>
                            <td class="text-center">{{ $customer['count'] }}</td>
                            <td class="text-right">R$ {{ number_format($customer['total'] / $customer['count'], 2, ',', '.') }}</td>
                            <td class="text-center">{{ $totalSales > 0 ? number_format(($customer['total'] / $totalSales) * 100, 1) : 0 }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                Nenhuma venda registrada no período
            </div>
        @endif
    </div>

    <!-- Vendas por Forma de Pagamento -->
    @if($salesData['byPayment']->count() > 0)
    <div class="section">
        <h2>Vendas por Forma de Pagamento</h2>
        <table>
            <thead>
                <tr>
                    <th>Forma de Pagamento</th>
                    <th class="text-right">Total (R$)</th>
                    <th class="text-center">Quantidade</th>
                    <th class="text-center">% do Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesData['byPayment'] as $payment)
                    <tr>
                        <td class="font-bold">{{ ucfirst($payment->payment_mode) }}</td>
                        <td class="text-right font-bold">R$ {{ number_format($payment->total, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $payment->count }}</td>
                        <td class="text-center">{{ $totalSales > 0 ? number_format(($payment->total / $totalSales) * 100, 1) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Vendas por Dia -->
    @if($salesData['byDay']->count() > 0)
    <div class="section">
        <h2>Vendas por Dia</h2>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th class="text-right">Total (R$)</th>
                    <th class="text-center">Quantidade</th>
                    <th class="text-right">Ticket Médio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesData['byDay'] as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}</td>
                        <td class="text-right font-bold">R$ {{ number_format($day->total, 2, ',', '.') }}</td>
                        <td class="text-center">{{ $day->count }}</td>
                        <td class="text-right">R$ {{ number_format($day->total / $day->count, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Resumo Final -->
    <div class="section">
        <h2>Resumo Final</h2>
        <div class="bg-gray-50 border rounded p-4">
            <p><strong>Período analisado:</strong> {{ $periods['label'] }}</p>
            <p><strong>Usuário responsável:</strong> {{ $user->name }} ({{ $user->email }})</p>
            <p><strong>Total de vendas realizadas:</strong> R$ {{ number_format($totalSales, 2, ',', '.') }}</p>
            <p><strong>Número total de transações:</strong> {{ number_format($totalSalesCount) }}</p>
            <p><strong>Ticket médio por venda:</strong> R$ {{ number_format($averageTicket, 2, ',', '.') }}</p>
            <p><strong>Número de clientes atendidos:</strong> {{ $salesByCustomer->count() }}</p>
            @if($salesByCustomer->count() > 0)
                <p><strong>Cliente com maior faturamento:</strong> {{ $salesByCustomer->first()['customer'] }} (R$ {{ number_format($salesByCustomer->first()['total'], 2, ',', '.') }})</p>
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
