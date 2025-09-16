@extends('dashboard.layout')

@section('content')
    <div class="container mx-auto py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Frente de Caixa</h1>
            @if(!$registers->where('status', 'open')->count())
            <!-- Botão para abrir novo caixa -->
            <button onclick="document.getElementById('modal-abertura').classList.remove('hidden')" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-semibold shadow transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Abrir Caixa
            </button>
            @else
            <span class="text-green-700 font-semibold">Há um caixa aberto</span>
            @endif
        </div>
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Operador</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aberto em</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fechado em</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valor Inicial</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valor Final</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($registers as $register)
                    <tr>
                        <td class="px-4 py-2">{{ $register->id }}</td>
                        <td class="px-4 py-2">{{ $register->user->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $register->opened_at }}</td>
                        <td class="px-4 py-2">{{ $register->closed_at ?? '-' }}</td>
                        <td class="px-4 py-2">R$ {{ number_format($register->initial_amount, 2, ',', '.') }}</td>
                        <td class="px-4 py-2">{{ $register->final_amount !== null ? 'R$ '.number_format($register->final_amount, 2, ',', '.') : '-' }}</td>
                        <td class="px-4 py-2">
                            @if($register->status == 'open')
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Aberto</span>
                            @else
                                <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs font-semibold">Fechado</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 flex flex-wrap gap-2">
                            <a href="{{ route('caixa.show', $register->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 text-xs font-semibold transition" title="Ver detalhes">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                Ver
                            </a>
                            <a href="{{ route('caixa.movements', $register->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 text-xs font-semibold transition" title="Movimentações">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16l4-4m0 0l-4-4m4 4H8" /></svg>
                                Mov.
                            </a>
                            <a href="{{ route('caixa.report', $register->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 text-xs font-semibold transition" title="Relatório">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4m0 0V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2h4" /></svg>
                                Relatório
                            </a>
                            @if($register->status == 'open')
                            <form action="{{ route('caixa.close', $register->id) }}" method="POST" onsubmit="return confirm('Fechar este caixa?')" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-100 text-red-700 rounded hover:bg-red-200 text-xs font-semibold transition" title="Fechar Caixa">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    Fechar
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">Nenhum caixa encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal de abertura de caixa -->
        <div id="modal-abertura" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm relative">
                <button onclick="document.getElementById('modal-abertura').classList.add('hidden')" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Abrir Caixa
                </h2>
                <form action="{{ route('caixa.open') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="initial_amount" class="block text-sm font-medium text-gray-700 mb-1">Valor Inicial (Sangria)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" /></svg>
                            </span>
                            <input type="number" name="initial_amount" id="initial_amount" step="0.01" min="0" placeholder="Digite o valor inicial do caixa" class="pl-10 pr-3 py-2 border rounded w-full focus:ring-2 focus:ring-green-500 focus:border-green-500" required autofocus>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modal-abertura').classList.add('hidden')" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white font-semibold hover:bg-green-700">Abrir Caixa</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
