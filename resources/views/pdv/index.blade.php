@extends('dashboard.layout')
@section('content')
<div class="flex flex-col md:flex-row gap-6 h-[80vh]">
    <!-- Lista de Pedidos (Carrinho Atual) -->
    <div class="flex-1 bg-blue-50 rounded-lg shadow flex flex-col min-w-[260px]">
        <div class="p-4 border-b border-blue-200 bg-blue-700 text-white rounded-t-lg">
            <form action="{{ route('pdv.addItem') }}" method="POST" class="flex flex-wrap gap-2 items-center mb-2">
                @csrf
                <div class="w-full flex gap-2 items-center mb-2">
                    <label class="text-xs font-semibold">Vendedor</label>
                    <select name="seller_id" class="form-select px-2 py-1 rounded text-xs flex-1" required>
                        <option value="">Selecione o Vendedor</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="text-xs font-semibold">Produto</label>
                <select name="product_id" class="form-select px-2 py-1 rounded text-xs" required>
                    <option value="">Selecione</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} - R$ {{ number_format($product->price,2,',','.') }}</option>
                    @endforeach
                </select>
                <label class="text-xs font-semibold">Qtd</label>
                <input type="number" name="quantity" value="1" min="1" class="form-input px-2 py-1 rounded text-xs w-16" required>
                <button class="btn btn-xs bg-blue-600 hover:bg-blue-800 text-white px-3 py-1 rounded ml-2">Adicionar</button>
            </form>
        </div>
        <div class="flex-1 overflow-y-auto p-2">
            <table class="min-w-full text-xs text-gray-700">
                <thead>
                    <tr class="bg-blue-100 text-blue-900">
                        <th class="p-2 font-bold">Produto</th>
                        <th class="p-2 font-bold">Qtd</th>
                        <th class="p-2 font-bold">Unitário</th>
                        <th class="p-2 font-bold">Total</th>
                        <th class="p-2 font-bold">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @if($sale && $sale->items->count())
                        @foreach($sale->items as $item)
                        <tr class="border-b">
                            <td class="p-2">{{ $item->product->name }}</td>
                            <td class="p-2">{{ $item->quantity }}</td>
                            <td class="p-2">R$ {{ number_format($item->unit_price,2,',','.') }}</td>
                            <td class="p-2">R$ {{ number_format($item->total_price,2,',','.') }}</td>
                            <td class="p-2">
                                <form action="{{ route('pdv.removeItem', $item->id) }}" method="POST" onsubmit="return confirm('Remover item?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">Remover</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="5" class="text-center text-gray-400">Nenhum item no carrinho</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="flex flex-wrap gap-2 p-4 border-t border-blue-200 bg-blue-50 rounded-b-lg justify-end">
            <form action="{{ route('pdv.start') }}" method="POST">
                @csrf
                <button class="btn btn-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded">Nova Venda</button>
            </form>
        </div>
    </div>
    <!-- Detalhes do Pedido (Resumo, Desconto, Pagamento, Finalizar) -->
    <div class="flex-1 bg-white rounded-lg shadow flex flex-col min-w-[320px]">
        <div class="p-4 border-b border-blue-200 bg-blue-700 text-white rounded-t-lg">
            <div class="flex flex-wrap gap-4 items-center mb-1">
                <span class="font-bold text-sm">Resumo da Venda</span>
            </div>
        </div>
        <div class="flex-1 overflow-y-auto p-4">
            <div class="flex flex-wrap gap-4 mb-2 text-sm">
                <div><span class="font-bold">Total:</span> R$ {{ $sale ? number_format($sale->total,2,',','.') : '0,00' }}</div>
                <div><span class="font-bold">Desconto:</span> R$ {{ $sale ? number_format($sale->discount,2,',','.') : '0,00' }}</div>
                <div><span class="font-bold">Total Final:</span> <span class="text-green-700">R$ {{ $sale ? number_format($sale->final_total,2,',','.') : '0,00' }}</span></div>
            </div>
            @if($sale)
            <form action="{{ route('pdv.discount') }}" method="POST" class="flex gap-2 mb-4">
                @csrf
                <input type="number" name="discount" value="{{ $sale->discount }}" min="0" step="0.01" class="form-input px-2 py-1 rounded text-xs w-24" placeholder="Desconto">
                <button class="btn btn-xs bg-yellow-400 hover:bg-yellow-500 text-gray-900 px-3 py-1 rounded">Aplicar Desconto</button>
            </form>
            <form action="{{ route('pdv.addPayment') }}" method="POST" class="flex gap-2 mb-4">
                @csrf
                <select name="payment_type" class="form-select px-2 py-1 rounded text-xs" required>
                    <option value="">Forma de Pagamento</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="cartao">Cartão</option>
                    <option value="pix">Pix</option>
                </select>
                <input type="number" name="amount" min="0.01" step="0.01" class="form-input px-2 py-1 rounded text-xs w-24" placeholder="Valor" required>
                <button class="btn btn-xs bg-blue-700 hover:bg-blue-900 text-white px-3 py-1 rounded">Adicionar Pagamento</button>
            </form>
            <table class="min-w-full text-xs text-gray-700 mb-3">
                <thead>
                    <tr class="bg-blue-100 text-blue-900">
                        <th class="p-2 font-bold">Tipo</th>
                        <th class="p-2 font-bold">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->payments as $pay)
                    <tr class="border-b">
                        <td class="p-2">{{ ucfirst($pay->payment_type) }}</td>
                        <td class="p-2">R$ {{ number_format($pay->amount,2,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <form action="{{ route('pdv.finalize') }}" method="POST">
                @csrf
                <button class="btn btn-xs bg-green-700 hover:bg-green-900 text-white px-4 py-2 rounded w-full mt-2">Finalizar Venda</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
