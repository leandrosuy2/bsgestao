@extends('dashboard.layout')
@section('content')
<h1>Histórico de Vendas PDV</h1>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Operador</th>
            <th>Total</th>
            <th>Desconto</th>
            <th>Total Final</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale->id }}</td>
            <td>{{ $sale->user->name ?? '-' }}</td>
            <td>R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
            <td>R$ {{ number_format($sale->discount, 2, ',', '.') }}</td>
            <td>R$ {{ number_format($sale->final_total, 2, ',', '.') }}</td>
            <td>{{ $sale->status == 'completed' ? 'Finalizada' : 'Cancelada' }}</td>
            <td>{{ $sale->sold_at }}</td>
            <td><a href="{{ route('pdv.receipt', $sale->id) }}" class="btn btn-info btn-sm">Comprovante</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $sales->links() }}
@endsection
