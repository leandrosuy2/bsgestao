@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Vendas a Prazo</h1>
            <div class="flex space-x-4">
                <button onclick="filterSales('all')" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    Todas
                </button>
                <button onclick="filterSales('due_soon')" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    Vencendo
                </button>
                <button onclick="filterSales('overdue')" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                    Vencidas
                </button>
            </div>
        </div>

        <!-- Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-600">Total a Prazo</p>
                        <p class="text-lg font-bold text-blue-900">{{ $totalInstallments }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-600">Valor Total</p>
                        <p class="text-lg font-bold text-green-900">R$ {{ number_format($totalAmount, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-600">Vencendo (7 dias)</p>
                        <p class="text-lg font-bold text-yellow-900">{{ $dueSoon }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-600">Vencidas</p>
                        <p class="text-lg font-bold text-red-900">{{ $overdue }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                    <input type="date" id="date_from" class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                    <input type="date" id="date_to" class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status_filter" class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Todos</option>
                        <option value="normal">Normal</option>
                        <option value="due_soon">Vencendo</option>
                        <option value="overdue">Vencida</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button onclick="applyFilters()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                    Aplicar Filtros
                </button>
                <button onclick="clearFilters()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors ml-2">
                    Limpar
                </button>
            </div>
        </div>

        <!-- Tabela -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Venda
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Valor
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vencimento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="sales-table">
                    @foreach($sales as $sale)
                        <tr class="hover:bg-gray-50 sale-row" 
                            data-status="{{ $sale->isOverdue() ? 'overdue' : ($sale->getDaysUntilDue() <= 7 ? 'due_soon' : 'normal') }}"
                            data-date="{{ $sale->sold_at->format('Y-m-d') }}"
                            data-due-date="{{ $sale->installment_due_date ? $sale->installment_due_date->format('Y-m-d') : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-receipt text-gray-400"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">#{{ $sale->id }}</div>
                                        <div class="text-sm text-gray-500">{{ $sale->user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $sale->sold_at->format('d/m/Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $sale->sold_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">R$ {{ number_format($sale->final_total, 2, ',', '.') }}</div>
                                <div class="text-sm text-gray-500">A prazo: R$ {{ number_format($sale->getInstallmentAmount(), 2, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $sale->installment_due_date ? $sale->installment_due_date->format('d/m/Y') : '-' }}</div>
                                @if($sale->installment_due_date)
                                    <div class="text-sm text-gray-500">
                                        @php
                                            $days = $sale->getDaysUntilDue();
                                        @endphp
                                        @if($days > 0)
                                            Em {{ $days }} dia(s)
                                        @elseif($days === 0)
                                            Vence hoje
                                        @else
                                            Vencida há {{ abs($days) }} dia(s)
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sale->isOverdue())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Vencida
                                    </span>
                                @elseif($sale->getDaysUntilDue() <= 7)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Vencendo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Normal
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="viewSale({{ $sale->id }})" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="printCupom({{ $sale->id }})" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    <button onclick="editInstallment({{ $sale->id }})" class="text-yellow-600 hover:text-yellow-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    </div>
</div>

<script>
function filterSales(type) {
    const rows = document.querySelectorAll('.sale-row');
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        let show = true;
        
        switch(type) {
            case 'due_soon':
                show = status === 'due_soon';
                break;
            case 'overdue':
                show = status === 'overdue';
                break;
            case 'all':
            default:
                show = true;
                break;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function applyFilters() {
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    const statusFilter = document.getElementById('status_filter').value;
    
    const rows = document.querySelectorAll('.sale-row');
    
    rows.forEach(row => {
        const saleDate = row.getAttribute('data-date');
        const status = row.getAttribute('data-status');
        let show = true;
        
        // Filtro por data
        if (dateFrom && saleDate < dateFrom) show = false;
        if (dateTo && saleDate > dateTo) show = false;
        
        // Filtro por status
        if (statusFilter && status !== statusFilter) show = false;
        
        row.style.display = show ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('date_from').value = '';
    document.getElementById('date_to').value = '';
    document.getElementById('status_filter').value = '';
    
    const rows = document.querySelectorAll('.sale-row');
    rows.forEach(row => {
        row.style.display = '';
    });
}

function viewSale(id) {
    window.location.href = `/pdv/receipt/${id}`;
}

function printCupom(id) {
    window.open(`/pdv/cupom/${id}`, '_blank');
}

function editInstallment(id) {
    // Implementar edição de prazo
    alert('Funcionalidade em desenvolvimento');
}
</script>
@endsection
