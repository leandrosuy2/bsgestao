<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cupom de Venda #{{ $sale->id }}</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
            background-color: #f8f9fa;
        }
        .cupom {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .empresa {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .info {
            font-size: 10px;
            color: #666;
        }
        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        .item-nome {
            flex: 1;
            margin-right: 10px;
        }
        .item-valor {
            white-space: nowrap;
        }
        .linha {
            border-bottom: 1px dashed #ccc;
            margin: 10px 0;
        }
        .total {
            font-weight: bold;
            font-size: 12px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }
        .pagamento {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
        .pagamento-header {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .pagamento-item {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            margin-bottom: 3px;
        }
        .pagamento-prazo {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 8px;
            margin-top: 10px;
        }
        .pagamento-prazo-header {
            font-weight: bold;
            color: #856404;
            font-size: 12px;
            margin-bottom: 5px;
        }
        .pagamento-prazo-info {
            font-size: 10px;
            color: #856404;
            margin-bottom: 3px;
        }
        .rodape {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }
        .no-print {
            text-align: center;
            margin-top: 20px;
        }
        .btn-print {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .btn-print:hover {
            background-color: #0056b3;
        }
        @media print {
            body {
                background-color: white;
                max-width: none;
                margin: 0;
                padding: 0;
            }
            .cupom {
                border: none;
                border-radius: 0;
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="cupom">
        <div class="header">
            <div class="empresa">{{ Auth::user()->company->name ?? 'EMPRESA' }}</div>
            <div class="info">CUPOM NÃO FISCAL</div>
            <div class="info">{{ now()->format('d/m/Y H:i:s') }}</div>
            <div class="info">Venda #{{ $sale->id }}</div>
            @if($sale->seller)
            <div class="info">Vendedor: {{ $sale->seller->name }}</div>
            @endif
        </div>

        <div class="itens">
            @foreach($sale->items as $item)
                <div class="item">
                    <div class="item-nome">
                        {{ $item->product_name ?? ($item->product->name ?? 'Produto') }}
                        <br>
                        <small>{{ $item->quantity }}x R$ {{ number_format($item->unit_price, 2, ',', '.') }}</small>
                        @if($item->has_discount)
                            <br>
                            <small style="color: #dc3545;">
                                Desconto: {{ $item->formatted_discount }}
                            </small>
                        @endif
                    </div>
                    <div class="item-valor">
                        @if($item->has_discount)
                            <div style="text-decoration: line-through; color: #666; font-size: 10px;">
                                R$ {{ number_format($item->total_price, 2, ',', '.') }}
                            </div>
                            <div style="color: #dc3545; font-weight: bold;">
                                R$ {{ number_format($item->final_price, 2, ',', '.') }}
                            </div>
                        @else
                            R$ {{ number_format($item->final_price, 2, ',', '.') }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="linha"></div>

        <div class="total">
            <div class="item">
                <span>SUBTOTAL:</span>
                <span>R$ {{ number_format($sale->items->sum('total_price'), 2, ',', '.') }}</span>
            </div>
            @if($sale->items->sum('discount_amount') > 0)
                <div class="item">
                    <span>DESCONTO PRODUTOS:</span>
                    <span>R$ {{ number_format($sale->items->sum('discount_amount'), 2, ',', '.') }}</span>
                </div>
            @endif
            @if($sale->discount > 0)
                <div class="item">
                    <span>DESCONTO GERAL:</span>
                    <span>R$ {{ number_format($sale->discount, 2, ',', '.') }}</span>
                </div>
            @endif
            <div class="item" style="font-size: 14px; font-weight: bold;">
                <span>TOTAL:</span>
                <span>R$ {{ number_format($sale->final_total, 2, ',', '.') }}</span>
            </div>
        </div>

        <div class="pagamento">
            <div class="pagamento-header">FORMA DE PAGAMENTO</div>
            @foreach($sale->payments as $payment)
                <div class="pagamento-item">
                    <span>{{ strtoupper(str_replace('_', ' ', $payment->payment_type)) }}</span>
                    <span>R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                </div>
            @endforeach
        </div>

        @if($sale->payment_mode === 'installment')
            <div class="pagamento-prazo">
                <div class="pagamento-prazo-header">⚠️ PAGAMENTO A PRAZO</div>
                <div class="pagamento-prazo-info">
                    <strong>Vencimento:</strong> {{ \Carbon\Carbon::parse($sale->installment_due_date)->format('d/m/Y') }}
                </div>
                <div class="pagamento-prazo-info">
                    <strong>Valor a Receber:</strong> R$ {{ number_format($sale->payments->where('payment_type', 'prazo')->sum('amount'), 2, ',', '.') }}
                </div>
            </div>
        @endif

        

        <div class="rodape">
            <div>Atendente: {{ $sale->user->name }}</div>
            <div>Caixa: {{ $sale->cashRegister->id ?? 'N/A' }}</div>
            <div style="margin-top: 10px;">
                ════════════════════════════════
            </div>
             @if($sale->installment_notes ?? $sale->observacoes_prazo)
                <div class="pagamento-prazo-info">
                    <p>Observações: </p>{{ $sale->installment_notes ?? $sale->observacoes_prazo }}
                </div>
            @endif
            
            <div>OBRIGADO PELA PREFERÊNCIA!</div>
          
           

            <div>Volte sempre!</div>
        </div>
    </div>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            🖨️ Imprimir Cupom
        </button>
    </div>

    <script>
        // Auto-imprimir quando a página carrega
        window.onload = function() {
            // Aguardar um pouco para garantir que o conteúdo foi carregado
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
