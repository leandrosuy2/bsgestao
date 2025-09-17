<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Romaneio de Entrega #{{ $deliveryReceipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .romaneio {
            background-color: white;
            border: 2px solid #000;
            border-radius: 8px;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }
        .info-group h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
        }
        .info-group p {
            margin: 5px 0;
            font-size: 12px;
            color: #555;
        }
        .items-section {
            margin-bottom: 25px;
        }
        .items-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        .items-table th {
            background-color: #f1f1f1;
            font-weight: bold;
            text-transform: uppercase;
        }
        .items-table .item-nome {
            max-width: 200px;
            word-wrap: break-word;
        }
        .items-table .center {
            text-align: center;
        }
        .items-table .right {
            text-align: right;
        }
        .summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .summary-box {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f8f9fa;
        }
        .summary-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
            padding-top: 40px;
            border-top: 1px solid #000;
        }
        .signature-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .observations {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
        }
        .observations h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #856404;
        }
        .observations p {
            margin: 0;
            font-size: 12px;
            color: #856404;
            line-height: 1.4;
        }
        @media print {
            body {
                background-color: white;
                padding: 0;
            }
            .romaneio {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="romaneio">
        <!-- Header -->
        <div class="header">
            <div class="title">ROMANEIO DE ENTREGA</div>
            <div class="subtitle">{{ $deliveryReceipt->receipt_number }}</div>
            <div class="subtitle">{{ $deliveryReceipt->delivery_date->format('d/m/Y H:i') }}</div>
        </div>

        <!-- Informações -->
        <div class="info-section">
            <div class="info-group">
                <h4>📦 Informações da Entrega</h4>
                <p><strong>Número:</strong> {{ $deliveryReceipt->receipt_number }}</p>
                <p><strong>Data:</strong> {{ $deliveryReceipt->delivery_date->format('d/m/Y H:i') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($deliveryReceipt->status) }}</p>
                <p><strong>Status do Pagamento:</strong> 
                    @switch($deliveryReceipt->payment_status ?? 'paid')
                        @case('paid')
                            <span style="color: green;">✓ Pago</span>
                            @break
                        @case('installment')
                            <span style="color: orange;">💳 A Prazo</span>
                            @break
                        @case('pending')
                            <span style="color: red;">⏳ Pendente</span>
                            @break
                        @case('overdue')
                            <span style="color: red;">⚠️ Atrasado</span>
                            @break
                        @default
                            <span style="color: gray;">❓ Não definido</span>
                    @endswitch
                </p>
                <p><strong>Venda PDV:</strong> #{{ $sale->id }}</p>
            </div>
            
            <div class="info-group">
                <h4>👤 Cliente</h4>
                @if($deliveryReceipt->customer_name && $deliveryReceipt->customer_name !== 'Cliente não informado')
                    <p><strong>Nome:</strong> {{ $deliveryReceipt->customer_name }}</p>
                    <p><strong>CPF/CNPJ:</strong> {{ $deliveryReceipt->customer_cpf_cnpj ?: 'Não informado' }}</p>
                    <p><strong>Telefone:</strong> {{ $deliveryReceipt->customer_phone ?: 'Não informado' }}</p>
                    <p><strong>Email:</strong> {{ $deliveryReceipt->customer_email ?: 'Não informado' }}</p>
                @elseif($sale->customer)
                    <p><strong>Nome:</strong> {{ $sale->customer->name }}</p>
                    <p><strong>CPF/CNPJ:</strong> {{ $sale->customer->cpf_cnpj ?? 'Não informado' }}</p>
                    <p><strong>Telefone:</strong> {{ $sale->customer->phone ?? 'Não informado' }}</p>
                    <p><strong>Email:</strong> {{ $sale->customer->email ?? 'Não informado' }}</p>
                @else
                    <p><strong>Cliente:</strong> Não informado</p>
                @endif
                <p><strong>Vendedor:</strong> {{ $sale->user->name }}</p>
            </div>
        </div>

        <!-- Endereço de Entrega -->
        @if($deliveryReceipt->delivery_address || $deliveryReceipt->delivery_city)
        <div class="info-section">
            <div class="info-group">
                <h4>📍 Endereço de Entrega</h4>
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
        </div>
        @endif

        <!-- Itens -->
        <div class="items-section">
            <div class="items-title">📋 Itens para Entrega</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th class="center">#</th>
                        <th>Produto</th>
                        <th class="center">Qtd<br>Esperada</th>
                        <th class="center">Qtd<br>Entregue</th>
                        <th class="right">Valor Unit.</th>
                        <th class="right">Total</th>
                        <th class="center">✓</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($deliveryReceipt->items as $index => $item)
                        <tr>
                            <td class="center">{{ $index + 1 }}</td>
                            <td class="item-nome">
                                {{ $item->product_name }}
                                @if($item->unit_price > 0)
                                    <br><small>R$ {{ number_format($item->unit_price, 2, ',', '.') }} cada</small>
                                @endif
                            </td>
                            <td class="center">{{ $item->quantity_expected }}</td>
                            <td class="center">{{ $item->quantity_received }}</td>
                            <td class="right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                            <td class="right">
                                @if($item->total_price > 0)
                                    R$ {{ number_format($item->total_price, 2, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="center">
                                <span style="color: {{ $item->checked ? '#28a745' : '#6c757d' }} !important; font-weight: bold; font-size: 16px; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">
                                    {{ $item->checked ? '✓' : '☐' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Resumo -->
        <div class="summary">
            <div class="summary-box">
                <div class="summary-title">📊 Resumo da Entrega</div>
                <div class="summary-item">
                    <span>Total de Itens:</span>
                    <span>{{ $deliveryReceipt->total_items }}</span>
                </div>
                <div class="summary-item">
                    <span>Itens Conferidos:</span>
                    <span>{{ $deliveryReceipt->checked_items }}</span>
                </div>
                <div class="summary-item">
                    <span>Progresso:</span>
                    <span>{{ number_format($deliveryReceipt->progress_percentage, 1) }}%</span>
                </div>
            </div>
            
            <div class="summary-box">
                <div class="summary-title">💰 Valores da Venda</div>
                <div class="summary-item">
                    <span>Subtotal:</span>
                    <span>R$ {{ number_format($sale->items->sum('total_price'), 2, ',', '.') }}</span>
                </div>
                @if($sale->items->sum('discount_amount') > 0)
                <div class="summary-item">
                    <span>Desconto Produtos:</span>
                    <span>R$ {{ number_format($sale->items->sum('discount_amount'), 2, ',', '.') }}</span>
                </div>
                @endif
                @if($sale->discount > 0)
                <div class="summary-item">
                    <span>Desconto Geral:</span>
                    <span>R$ {{ number_format($sale->discount, 2, ',', '.') }}</span>
                </div>
                @endif
                <div class="summary-item" style="font-weight: bold; border-top: 1px solid #ddd; padding-top: 5px; margin-top: 5px;">
                    <span>Total Final:</span>
                    <span>R$ {{ number_format($sale->final_total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Observações -->
        @if($deliveryReceipt->notes)
        <div class="observations">
            <h4>📝 Observações</h4>
            <p>{{ $deliveryReceipt->notes }}</p>
        </div>
        @endif

        <!-- Assinaturas -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-label">Assinatura do Entregador</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">Assinatura do Cliente</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="font-size: 10px; color: #666; margin: 0;">
                Documento gerado automaticamente em {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>

    <script>
        // Auto-print quando carregado
        window.onload = function() {
            if (window.location.search.includes('print=1')) {
                window.print();
            }
        }
    </script>
</body>
</html>
