@extends('dashboard.layout')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ isset($seller) ? 'Editar' : 'Novo' }} Vendedor</h1>
        <a href="{{ route('sellers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    @include('dashboard.alerts')

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($seller) ? route('sellers.update', $seller) : route('sellers.store') }}" method="POST">
                @csrf
                @if(isset($seller))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nome *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                            id="name" name="name" required
                            value="{{ old('name', $seller->name ?? '') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                            id="email" name="email"
                            value="{{ old('email', $seller->email ?? '') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="phone" class="form-label">Telefone</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                            id="phone" name="phone"
                            value="{{ old('phone', $seller->phone ?? '') }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="document" class="form-label">CPF/CNPJ</label>
                        <input type="text" class="form-control @error('document') is-invalid @enderror" 
                            id="document" name="document"
                            value="{{ old('document', $seller->document ?? '') }}">
                        @error('document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="commission_rate" class="form-label">Taxa de Comissão (%) *</label>
                        <input type="number" class="form-control @error('commission_rate') is-invalid @enderror" 
                            id="commission_rate" name="commission_rate" required
                            min="0" max="100" step="0.01"
                            value="{{ old('commission_rate', $seller->commission_rate ?? '0') }}">
                        @error('commission_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if(isset($seller))
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                                {{ $seller->active ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">Vendedor Ativo</label>
                        </div>
                    </div>
                @endif

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
