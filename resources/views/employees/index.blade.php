@extends('dashboard.layout')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <span class="bg-gray-800 p-2 rounded-full shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </span>
        <h1 class="text-2xl font-bold text-gray-800">Funcionários</h1>
    </div>
    <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-800 to-gray-700 text-white px-5 py-2 rounded-xl hover:from-gray-900 hover:to-gray-800 transition font-semibold shadow-lg ring-1 ring-gray-900/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Novo Funcionário
    </a>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

<div class="overflow-x-auto bg-white rounded-xl shadow-sm">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Nome</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">CPF</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">E-mail</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Telefone</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Cargo</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Permissão</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Ativo</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Admissão</th>
                <th class="px-4 py-3 text-right"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $employee)
                <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $employee->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $employee->cpf }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $employee->email }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $employee->phone ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $employee->role }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ ucfirst($employee->permission_level) }}</td>
                    <td class="px-4 py-3">
                        @if($employee->active)
                            <span class="inline-block px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">Ativo</span>
                        @else
                            <span class="inline-block px-2 py-1 rounded bg-red-100 text-red-800 text-xs font-medium">Inativo</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ \Carbon\Carbon::parse($employee->admission_date)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-medium px-2 py-1 rounded hover:bg-blue-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m-2 2h6"/>
                                </svg>
                                Editar
                            </a>
                            <form action="{{ route('employees.toggle-status', $employee) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1 text-white bg-gradient-to-r from-gray-600 to-gray-500 hover:from-gray-700 hover:to-gray-600 font-semibold px-3 py-1.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $employee->active ? 'Desativar' : 'Ativar' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-400">Nenhum funcionário cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($employees->hasPages())
    <div class="mt-6">
        {{ $employees->links() }}
    </div>
@endif
@endsection
