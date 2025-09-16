@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Página de Testes Sicredi</h2>
    <div class="row">
        <div class="col-md-6">
            <h4>Criar Boleto</h4>
            <form method="POST" action="{{ route('sicredi.teste.criar') }}">
                @csrf
                <textarea name="payload" class="form-control" rows="10" placeholder="JSON do payload conforme manual"></textarea>
                <input type="text" name="access_token" class="form-control mt-2" placeholder="Access Token (opcional)">
                <button type="submit" class="btn btn-success mt-2">Criar Boleto</button>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Consultar Boleto</h4>
            <form method="POST" action="{{ route('sicredi.teste.consultar') }}">
                @csrf
                <input type="text" name="codigoBeneficiario" class="form-control" placeholder="Código Beneficiário">
                <input type="text" name="nossoNumero" class="form-control mt-2" placeholder="Nosso Número">
                <input type="text" name="access_token" class="form-control mt-2" placeholder="Access Token (opcional)">
                <button type="submit" class="btn btn-primary mt-2">Consultar</button>
            </form>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <h4>Listar Boletos</h4>
            <form method="POST" action="{{ route('sicredi.teste.listar') }}">
                @csrf
                <input type="text" name="access_token" class="form-control" placeholder="Access Token (opcional)">
                <button type="submit" class="btn btn-info mt-2">Listar</button>
            </form>
        </div>
        <div class="col-md-6">
            <h4>Baixar PDF do Boleto</h4>
            <form method="POST" action="{{ route('sicredi.teste.pdf') }}">
                @csrf
                <input type="text" name="linhaDigitavel" class="form-control" placeholder="Linha Digitável">
                <input type="text" name="access_token" class="form-control mt-2" placeholder="Access Token (opcional)">
                <button type="submit" class="btn btn-warning mt-2">Baixar PDF</button>
            </form>
        </div>
    </div>
    @if(session('result'))
        <div class="alert alert-secondary mt-4">
            <pre>{{ json_encode(session('result'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif
</div>
@endsection
