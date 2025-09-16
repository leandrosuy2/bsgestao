<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orçamento #{{ $quote->quote_number ?? $quote->id }}</title>
    <style>
        @page {
            margin: 8mm 5mm;
            size: A4;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            font-size: 11px;
            line-height: 1.3;
            background: linear-gradient(to bottom, #fb923c, #fdba74);
            min-height: 100vh;
        }

        .container {
            max-width: 100%;
            background: #fef3e2;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 0 auto;
            min-height: calc(100vh - 30px);
            box-sizing: border-box;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        .logo-subtitle {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        .quote-number {
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }

        /* Cliente Info */
        .cliente-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .cliente-info > div {
            flex: 1;
            margin-right: 20px;
        }

        .cliente-info > div:last-child {
            margin-right: 0;
        }

        .info-label {
            font-weight: bold;
            font-size: 10px;
            color: #666;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 11px;
            color: #000;
            margin-bottom: 10px;
        }

        /* Tabela */
        .tabela {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #9ca3af;
        }

        th {
            background-color: #fdba74;
            color: #000;
            font-weight: bold;
            padding: 8px 6px;
            border: 1px solid #9ca3af;
            font-size: 10px;
            text-align: left;
        }

        td {
            padding: 6px 4px;
            border: 1px solid #9ca3af;
            font-size: 10px;
            vertical-align: top;
        }

        .item-description {
            max-width: 200px;
            word-wrap: break-word;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Totais */
        .totals-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 15px;
        }

        .subtotal {
            font-size: 11px;
            font-weight: bold;
            color: #dc2626;
        }

        .final-total {
            font-size: 13px;
            font-weight: bold;
            color: #dc2626;
        }

        /* Info Extra */
        .info-extra {
            background-color: #fdba74;
            padding: 12px;
            border-radius: 6px;
            border: 2px dashed #fb923c;
            margin-bottom: 15px;
        }

        .info-extra-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-extra-item {
            margin-bottom: 8px;
        }

        .info-extra-label {
            font-weight: bold;
            font-size: 10px;
            color: #374151;
            margin-bottom: 3px;
        }

        .info-extra-value {
            font-size: 11px;
            color: #000;
            line-height: 1.2;
        }

        /* Print optimizations */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0;
                padding: 0;
                font-size: 11px;
            }
            
            .container {
                margin: 0;
                padding: 15px;
                height: auto;
                min-height: auto;
                page-break-inside: avoid;
            }
            
            .no-print {
                display: none !important;
            }
            
            table {
                page-break-inside: avoid;
            }
            
            .header {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                {{ $quote->company_id ?? '9' }}
                <div class="logo-subtitle">{{ $quote->company_subtitle ?? 'COMUNICAÇÃO VISUAL' }}</div>
            </div>
            <div class="quote-number">
                {{ $quote->quote_number ?? sprintf('%05d/%s', $quote->id, $quote->created_at->format('y')) }}
            </div>
        </header>

        <!-- Informações do Cliente -->
        <section class="cliente-info">
            <div>
                <div class="info-label">Contratante</div>
                <div class="info-value">{{ $quote->customer_name }}</div>
            </div>
            <div>
                <div class="info-label">Data</div>
                <div class="info-value">{{ $quote->created_at->format('d \d\e F \d\e Y') }}</div>
            </div>
            <div>
                <div class="info-label">Email</div>
                <div class="info-value">{{ $quote->customer_email ?? '-' }}</div>
            </div>
            <div>
                <div class="info-label">Telefone</div>
                <div class="info-value">{{ $quote->customer_phone ?? '-' }}</div>
            </div>
        </section>

        <!-- Tabela de Itens -->
        <section class="tabela">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Produto</th>
                        <th style="width: 12%;" class="text-center">Qt</th>
                        <th style="width: 19%;" class="text-center">Valor Unit.</th>
                        <th style="width: 19%;" class="text-center">Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote->items as $item)
                    <tr>
                        <td class="item-description">{{ $item->product_name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-center">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totais -->
            <div class="totals-section">
                <div>
                    @if($quote->discount > 0)
                        <div style="font-size: 12px; margin-bottom: 5px;">
                            <strong>Desconto: R$ {{ number_format($quote->discount, 2, ',', '.') }}</strong>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="subtotal">Sub-total: R$ {{ number_format($quote->total, 2, ',', '.') }}</div>
                    <div class="final-total">Total Final: R$ {{ number_format($quote->final_total, 2, ',', '.') }}</div>
                </div>
            </div>
        </section>

        <!-- Informações Extras -->
        <section class="info-extra">
            <div class="info-extra-grid">
                <div class="info-extra-item">
                    <div class="info-extra-label">Formas de pagamento</div>
                    <div class="info-extra-value">{{ $quote->payment_terms ?? '50% entrada + 50% na entrega. Aceitamos pix, dinheiro e cartão de crédito.' }}</div>
                </div>
                <div class="info-extra-item">
                    <div class="info-extra-label">Prazo de entrega</div>
                    <div class="info-extra-value">{{ $quote->delivery_time ?? '10 dias úteis ou menos' }}</div>
                </div>
                @if($quote->pix_key)
                <div class="info-extra-item">
                    <div class="info-extra-label">Chave Pix</div>
                    <div class="info-extra-value">{{ $quote->pix_key }}</div>
                </div>
                @endif
                @if($quote->notes)
                <div class="info-extra-item">
                    <div class="info-extra-label">Observações</div>
                    <div class="info-extra-value">{{ $quote->notes }}</div>
                </div>
                @endif
            </div>
            
            @if($quote->valid_until)
            <div style="text-align: center; margin-top: 15px; font-weight: bold; font-size: 11px;">
                Orçamento válido até: {{ $quote->valid_until->format('d/m/Y') }}
            </div>
            @endif
        </section>
    </div>
</body>
</html>
