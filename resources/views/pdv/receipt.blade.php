@extends('dashboard.layout')

@section('title', 'Comprovante de Venda')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Cabeçalho -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Comprovante de Venda PDV #{{ $sale->id }}</h1>
                <p class="text-sm text-gray-500">{{ $sale->sold_at->format('d/m/Y H:i:s') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <p class="text-sm font-medium text-gray-500">Operador</p>
                    <p class="text-base text-gray-900">{{ $sale->user->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Vendedor</p>
                    <p class="text-base text-gray-900">{{ $sale->seller->name ?? 'Não informado' }}</p>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-500">Cliente</p>
                    <p class="text-base text-gray-900">{{ $sale->customer->name ?? 'Cliente não identificado' }}</p>
                </div>
            </div>

            <!-- Itens -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Itens</h3>
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantidade</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor Unit.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sale->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-center">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">
                                    R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                    R$ {{ number_format($item->total_price, 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagamentos -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Pagamentos</h3>
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Forma de Pagamento</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sale->payments as $pay)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($pay->payment_type) }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 text-right">
                                    R$ {{ number_format($pay->amount, 2, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totais -->
            <div class="border-t pt-4">
                <div class="flex justify-between items-center text-sm mb-2">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="text-gray-900">R$ {{ number_format($sale->total, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm mb-2">
                    <span class="text-gray-600">Desconto:</span>
                    <span class="text-gray-900">R$ {{ number_format($sale->discount, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-base font-semibold pt-2 border-t">
                    <span class="text-gray-900">Total Final:</span>
                    <span class="text-gray-900">R$ {{ number_format($sale->final_total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="flex justify-center gap-3">
            <a href="{{ route('pdv.full') }}" 
               class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-lg font-medium text-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Venda
            </a>
            <a href="#" onclick="window.print()" 
               class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg font-medium text-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir
            </a>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .p-6, .p-6 * {
            visibility: visible;
        }
        .p-6 {
            position: absolute;
            left: 0;
            top: 0;
        }
        .flex.justify-center.gap-3 {
            display: none;
        }
    }
</style>
@endsection
