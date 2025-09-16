<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento #{{ $quote->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
            font-size: 12px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #000;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        
        .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .customer-info > div {
            flex: 1;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ORÇAMENTO</h1>
        <p>Nº {{ $quote->id }} - {{ $quote->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="customer-info">
        <div>
            <div class="info-section">
                <h3>Cliente:</h3>
                <p><strong>{{ $quote->customer_name }}</strong></p>
                @if($quote->customer_email)
                    <p>Email: {{ $quote->customer_email }}</p>
                @endif
                @if($quote->customer_phone)
                    <p>Telefone: {{ $quote->customer_phone }}</p>
                @endif
                @if($quote->customer_address)
                    <p>Endereço: {{ $quote->customer_address }}</p>
                @endif
            </div>
        </div>
        
        <div>
            <div class="info-section">
                <h3>Válido até:</h3>
                <p>{{ $quote->valid_until ? $quote->valid_until->format('d/m/Y') : 'Não especificado' }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Descrição</th>
                <th class="text-center">Qtd</th>
                <th class="text-right">Valor Unit.</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product->name ?? $item->description }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td class="text-right">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>TOTAL GERAL:</strong></td>
                <td class="text-right"><strong>R$ {{ number_format($quote->total_amount, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($quote->notes)
    <div class="info-section">
        <h3>Observações:</h3>
        <p>{{ $quote->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Este orçamento tem validade de 30 dias a partir da data de emissão.</p>
        <p>Obrigado pela preferência!</p>
    </div>
</body>
</html>
