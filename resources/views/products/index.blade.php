@extends('dashboard.layout')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div class="flex items-center gap-3">
        <span class="bg-gray-800 p-2 rounded-full shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/>
            </svg>
        </span>
        <h1 class="text-2xl font-bold text-gray-800">Produtos</h1>
        @if(request()->hasAny(['search', 'category', 'stock_status']))
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                Filtros ativos
            </span>
        @endif
    </div>
    <div class="flex gap-2">
        @php
            $lowStockCount = \App\Models\Product::where('company_id', auth()->user()->company_id)
                                               ->whereColumn('stock_quantity', '<=', 'min_stock')
                                               ->count();
        @endphp
        @if($lowStockCount > 0)
            <a href="{{ route('products.index', ['stock_status' => 'low']) }}" class="inline-flex items-center gap-2 bg-red-100 text-red-700 px-4 py-2 rounded-lg hover:bg-red-200 transition font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                {{ $lowStockCount }} com estoque baixo
            </a>
        @endif
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-gray-800 to-gray-700 text-white px-5 py-2 rounded-xl hover:from-gray-900 hover:to-gray-800 transition font-semibold shadow-lg ring-1 ring-gray-900/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Novo Produto
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-800 rounded shadow-sm">
        {{ session('success') }}
    </div>
@endif

<!-- Formulário de Busca -->
<div class="bg-white p-4 rounded-lg shadow-sm mb-6">
    <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
        <div class="flex flex-col md:flex-row gap-2">
            <div class="flex-1 md:flex-[5]">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Digite o nome, código ou categoria do produto..." 
                           class="pl-12 pr-4 py-4 w-full text-lg font-medium border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800 shadow-sm">
                </div>
            </div>
            
            <div class="w-28 md:w-28">
                <select name="category" class="w-full px-2 py-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent">
                    <option value="">Categoria</option>
                    @foreach(\App\Models\Category::where('company_id', auth()->user()->company_id)->get() as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ Str::limit($category->name, 12) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-24 md:w-24">
                <select name="stock_status" class="w-full px-2 py-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-transparent">
                    <option value="">Estoque</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Baixo</option>
                    <option value="zero" {{ request('stock_status') == 'zero' ? 'selected' : '' }}>Zerado</option>
                    <option value="good" {{ request('stock_status') == 'good' ? 'selected' : '' }}>OK</option>
                </select>
            </div>
            
            <div class="flex gap-1">
                <button type="submit" class="inline-flex items-center justify-center bg-gray-800 text-white w-16 h-16 rounded-lg hover:bg-gray-900 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
                
                @if(request()->hasAny(['search', 'category', 'stock_status']))
                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center bg-gray-100 text-gray-700 w-16 h-16 rounded-lg hover:bg-gray-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </form>
    
    @if(request()->hasAny(['search', 'category', 'stock_status']))
        <div class="mt-3 text-sm text-gray-600">
            <span class="font-medium">{{ $products->total() }}</span> produto(s) encontrado(s)
            @if(request('search'))
                para "<span class="font-medium text-gray-800">{{ request('search') }}</span>"
            @endif
            @if(request('category'))
                na categoria "<span class="font-medium text-gray-800">{{ \App\Models\Category::find(request('category'))->name ?? 'N/A' }}</span>"
            @endif
            @if(request('stock_status'))
                @php
                    $statusLabels = [
                        'low' => 'com estoque baixo',
                        'zero' => 'com estoque zerado',
                        'good' => 'com estoque adequado'
                    ];
                @endphp
                <span class="font-medium text-gray-800">{{ $statusLabels[request('stock_status')] ?? '' }}</span>
            @endif
        </div>
    @endif
</div>

<div class="overflow-x-auto bg-white rounded-xl shadow-sm">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Nome</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Código</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Categoria</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Unidade</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Preço Custo</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Preço Venda</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Estoque Atual</th>
                <th class="px-4 py-3 font-semibold text-gray-600 uppercase">Estoque Mínimo</th>
                <th class="px-4 py-3 text-right"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr class="border-b last:border-0 hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $product->internal_code }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $product->category->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $product->unit }}</td>
                    <td class="px-4 py-3 text-gray-600">R$ {{ number_format($product->cost_price, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-gray-600">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        @php
                            $stockColor = $product->stock_quantity <= $product->min_stock ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';
                        @endphp
                        <span class="inline-block px-2 py-1 rounded {{ $stockColor }} text-xs font-medium">
                            {{ $product->stock_quantity }} 
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs font-medium">
                            {{ $product->min_stock }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-1 text-white bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 font-semibold px-3 py-1.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6-6m2 2l-6 6m-2 2h6"/>
                                </svg>
                                Editar
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este produto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 text-white bg-gradient-to-r from-red-600 to-red-500 hover:from-red-700 hover:to-red-600 font-semibold px-3 py-1.5 rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Remover
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                        @if(request()->hasAny(['search', 'category', 'stock_status']))
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-500 mb-2">Nenhum produto encontrado</p>
                                <p class="text-sm text-gray-400 mb-4">Tente ajustar os filtros de busca</p>
                                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Limpar Filtros
                                </a>
                            </div>
                        @else
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-500 mb-2">Nenhum produto cadastrado</p>
                                <p class="text-sm text-gray-400 mb-4">Comece adicionando seu primeiro produto</p>
                                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Adicionar Produto
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($products->hasPages())
    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endif

<script>
// Atalho de teclado para busca rápida
document.addEventListener('keydown', function(e) {
    // Pressionar "/" para focar no campo de busca
    if (e.key === '/' && !e.ctrlKey && !e.metaKey && !e.altKey) {
        // Verificar se não estamos em um input/textarea
        if (document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            document.querySelector('input[name="search"]').focus();
        }
    }
    
    // Pressionar Escape para limpar busca
    if (e.key === 'Escape' && document.activeElement.name === 'search') {
        document.querySelector('input[name="search"]').value = '';
    }
});

// Auto-submit em filtros de select
document.querySelectorAll('select[name="category"], select[name="stock_status"]').forEach(function(select) {
    select.addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>
@endsection
