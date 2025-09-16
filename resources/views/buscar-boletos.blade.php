@extends('dashboard.layout')
@section('title', 'Buscar Boletos Sicredi')
@section('content')
<div class="w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-2xl px-12 py-10 mt-10">
    <h1 class="text-3xl font-extrabold mb-8 flex items-center gap-3 text-green-800">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-green-600"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75M9 6h.008v.008H9V6zm3.75 0h.008v.008h-.008V6zM12 3v3m0 0v3m0-3h3m-3 0H9m-6 9.75V6.375A2.625 2.625 0 015.625 3.75h12.75A2.625 2.625 0 0121 6.375v11.25A2.625 2.625 0 0118.375 20.25H5.625A2.625 2.625 0 013 17.625V15.75" /></svg>
        Buscar Boletos Sicredi
    </h1>
    <form method="GET" action="{{ route('buscar-boletos') }}" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <label class="block text-base font-semibold text-gray-700 mb-1">Cliente</label>
                <input type="text" name="cliente" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base bg-gray-50 text-gray-900">
            </div>
            <div>
                <label class="block text-base font-semibold text-gray-700 mb-1">Nosso Número</label>
                <input type="text" name="nosso_numero" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base bg-gray-50 text-gray-900">
            </div>
            <div>
                <label class="block text-base font-semibold text-gray-700 mb-1">Data de Vencimento</label>
                <input type="date" name="data_vencimento" class="w-full border border-gray-300 rounded-lg px-4 py-3 text-base bg-gray-50 text-gray-900">
            </div>
        </div>
        <div class="flex justify-end mt-8">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-bold py-3 px-10 rounded-xl shadow-lg text-lg transition-all">Buscar</button>
        </div>
    </form>
    @if(isset($boletos))
        <div class="mt-10">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Resultados</h2>
            <table class="min-w-full bg-white rounded-xl shadow">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-2 px-4">Cliente</th>
                        <th class="py-2 px-4">Nosso Número</th>
                        <th class="py-2 px-4">Valor</th>
                        <th class="py-2 px-4">Vencimento</th>
                        <th class="py-2 px-4">Status</th>
                        <th class="py-2 px-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boletos as $boleto)
                        <tr class="border-b">
                            <td class="py-2 px-4">{{ $boleto['pagador']['nome'] ?? '-' }}</td>
                            <td class="py-2 px-4">{{ $boleto['nossoNumero'] ?? '-' }}</td>
                            <td class="py-2 px-4">R$ {{ number_format($boleto['valor'],2,',','.') }}</td>
                            <td class="py-2 px-4">{{ $boleto['dataVencimento'] ?? '-' }}</td>
                            <td class="py-2 px-4">{{ $boleto['status'] ?? '-' }}</td>
                            <td class="py-2 px-4">
                                <a href="{{ route('baixar-boleto-pdf', ['nosso_numero' => $boleto['nossoNumero']]) }}" class="text-green-700 hover:underline">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-4 text-center text-gray-500">Nenhum boleto encontrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
