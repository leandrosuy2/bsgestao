@extends('dashboard.layout')
@section('content')
<h1>Venda PDV #{{ $sale->id }}</h1>
<h3>Adicionar Produto</h3>
<form action="{{ route('pdv.addItem', $sale->id) }}" method="POST" class="row g-3 mb-3">
    @csrf
    <div class="col-md-5">
        <select name="product_id" class="form-select" required>
            <option value="">Selecione o produto</option>
            @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <input type="number" name="quantity" class="form-control" min="1" value="1" required>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary">Adicionar</button>
    </div>
</form>
<h3>Itens da Venda</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Quantidade</th>
            <th>Valor Unitário</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
            <td>R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<h3>Desconto</h3>
<form action="{{ route('pdv.discount', $sale->id) }}" method="POST" class="row g-3 mb-3">
    @csrf
    <div class="col-md-3">
        <input type="number" name="discount" class="form-control" min="0" step="0.01" value="{{ $sale->discount }}">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-warning">Aplicar Desconto</button>
    </div>
</form>
<h3>Pagamentos</h3>
<form action="{{ route('pdv.addPayment', $sale->id) }}" method="POST" class="row g-3 mb-3">
    @csrf
    <div class="col-md-4">
        <select name="payment_type" class="form-select" required>
            <option value="">Forma de Pagamento</option>
            <option value="dinheiro">Dinheiro</option>
            <option value="cartao">Cartão</option>
            <option value="pix">Pix</option>
        </select>
    </div>
    <div class="col-md-3">
        <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-success">Adicionar Pagamento</button>
    </div>
</form>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tipo</th>
            <th>Valor</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->payments as $pay)
        <tr>
            <td>{{ ucfirst($pay->payment_type) }}</td>
            <td>R$ {{ number_format($pay->amount, 2, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<h3>Total da Venda</h3>
<p><strong>Total:</strong> R$ {{ number_format($sale->total, 2, ',', '.') }}</p>
<p><strong>Desconto:</strong> R$ {{ number_format($sale->discount, 2, ',', '.') }}</p>
<p><strong>Total Final:</strong> R$ {{ number_format($sale->final_total, 2, ',', '.') }}</p>
<form action="{{ route('pdv.finalize', $sale->id) }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary">Finalizar Venda</button>
</form>
@endsection
