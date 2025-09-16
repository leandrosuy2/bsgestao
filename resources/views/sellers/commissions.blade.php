@extends('dashboard.layout')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Relatório de Comissões</h1>
        <a href="{{ route('sellers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Data Inicial</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                        value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Data Final</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                        value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        @foreach($sellers as $data)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $data['seller']->name }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total de Vendas:</span>
                            <strong>{{ $data['total_sales'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Taxa de Comissão:</span>
                            <strong>{{ number_format($data['seller']->commission_rate, 2) }}%</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Total em Comissões:</span>
                            <strong class="text-success">
                                R$ {{ number_format($data['total_commission'], 2, ',', '.') }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
