<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Romaneio de Entrega</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section p {
            margin: 5px 0;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #4285f4;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #ddd;
        }
        .table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        .table tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        .observations {
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .observations h3 {
            margin-bottom: 10px;
        }
        .signatures {
            margin-top: 40px;
        }
        .signature-line {
            margin-top: 50px;
            border-bottom: 1px solid #000;
            width: 300px;
            display: inline-block;
        }
        .signature-container {
            margin-bottom: 20px;
        }
        .checked {
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ROMANEIO DE ENTREGA</h1>
    </div>

    <div class="info-section">
        <p><strong>Número:</strong> {{ $deliveryReceipt->receipt_number }}</p>
        <p><strong>Data:</strong> {{ $deliveryReceipt->delivery_date ? $deliveryReceipt->delivery_date->format('d/m/Y') : '' }}</p>
        <p><strong>Cliente:</strong> {{ $deliveryReceipt->customer_name ?: $deliveryReceipt->supplier_name }}</p>
        @if($deliveryReceipt->customer_cpf_cnpj)
            <p><strong>CPF/CNPJ:</strong> {{ $deliveryReceipt->customer_cpf_cnpj }}</p>
        @elseif($deliveryReceipt->supplier_cnpj)
            <p><strong>CNPJ:</strong> {{ $deliveryReceipt->supplier_cnpj }}</p>
        @endif
        @if($deliveryReceipt->customer_phone)
            <p><strong>Telefone:</strong> {{ $deliveryReceipt->customer_phone }}</p>
        @elseif($deliveryReceipt->supplier_contact)
            <p><strong>Contato:</strong> {{ $deliveryReceipt->supplier_contact }}</p>
        @endif
        @if($deliveryReceipt->customer_email)
            <p><strong>Email:</strong> {{ $deliveryReceipt->customer_email }}</p>
        @endif
    </div>

    @if($deliveryReceipt->delivery_address || $deliveryReceipt->delivery_city)
    <div class="info-section">
        <h3>Endereço de Entrega:</h3>
        @if($deliveryReceipt->delivery_address)
            <p><strong>Endereço:</strong> {{ $deliveryReceipt->delivery_address }}</p>
        @endif
        @if($deliveryReceipt->delivery_city)
            <p><strong>Cidade:</strong> {{ $deliveryReceipt->delivery_city }}</p>
        @endif
        @if($deliveryReceipt->delivery_state)
            <p><strong>Estado:</strong> {{ $deliveryReceipt->delivery_state }}</p>
        @endif
        @if($deliveryReceipt->delivery_zipcode)
            <p><strong>CEP:</strong> {{ $deliveryReceipt->delivery_zipcode }}</p>
        @endif
    </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Código</th>
                <th>Qtd. Esperada</th>
                <th>Qtd. Recebida</th>
                <th>Observações</th>
                <th>Conferido</th>
            </tr>
        </thead>
        <tbody>
            @foreach($deliveryReceipt->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->product_code ?? '' }}</td>
                    <td class="text-center">{{ $item->expected_quantity ?? ($item->quantity ?? '') }}</td>
                    <td class="text-center">{{ $item->received_quantity ?? '' }}</td>
                    <td>{{ $item->notes ?? '' }}</td>
                    <td class="text-center">{{ $item->checked ? '✓' : '✗' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($deliveryReceipt->notes)
        <div class="observations">
            <h3>Observações:</h3>
            <p>{{ $deliveryReceipt->notes }}</p>
        </div>
    @endif

    <div class="signatures">
        <div class="signature-container">
            <p><strong>Assinatura do Entregador:</strong> <span class="signature-line"></span></p>
        </div>
        <div class="signature-container">
            <p><strong>Assinatura do Recebedor:</strong> <span class="signature-line"></span></p>
        </div>
    </div>
</body>
</html>
