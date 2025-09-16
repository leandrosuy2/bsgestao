@extends('dashboard.layout')
@section('title', 'Editar Nota Fiscal')
@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2"><i class="fas fa-edit text-blue-500"></i> Editar Nota Fiscal</h1>
            <p class="text-gray-600">Edite os dados da nota fiscal e reenvie.</p>
        </div>
        <div class="text-sm text-gray-500 flex gap-2">
            <a href="{{ route('nfe.show', $nfe) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                Cancelar
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('nfe.update', $nfe) }}" id="nfe-form" class="space-y-6">
        @csrf
        @method('PUT')
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <strong>Erro!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Card: Dados Gerais da NFe -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-info-circle text-blue-500"></i> Dados Gerais</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm mb-1">Natureza da Operação *</label>
                        <input type="text" name="natureza_operacao" value="{{ old('natureza_operacao', $nfe->natureza_operacao) }}" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Tipo Documento *</label>
                            <select name="tipo_documento" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                <option value="1" {{ old('tipo_documento', $nfe->tipo_documento) == '1' ? 'selected' : '' }}>1 - Saída</option>
                                <option value="0" {{ old('tipo_documento', $nfe->tipo_documento) == '0' ? 'selected' : '' }}>0 - Entrada</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Finalidade *</label>
                            <select name="finalidade_emissao" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                <option value="1" {{ old('finalidade_emissao', $nfe->finalidade_emissao) == '1' ? 'selected' : '' }}>1 - Normal</option>
                                <option value="2" {{ old('finalidade_emissao', $nfe->finalidade_emissao) == '2' ? 'selected' : '' }}>2 - Complementar</option>
                                <option value="3" {{ old('finalidade_emissao', $nfe->finalidade_emissao) == '3' ? 'selected' : '' }}>3 - Ajuste</option>
                                <option value="4" {{ old('finalidade_emissao', $nfe->finalidade_emissao) == '4' ? 'selected' : '' }}>4 - Devolução</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Data de Emissão *</label>
                        <input type="date" name="data_emissao" value="{{ old('data_emissao', $nfe->data_emissao?->format('Y-m-d')) }}" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                    </div>
                </div>
            </div>

            <!-- Card: Dados do Destinatário -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-user text-purple-500"></i> Destinatário</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm mb-1">Nome/Razão Social *</label>
                        <input type="text" name="nome_destinatario" value="{{ old('nome_destinatario', $nfe->nome_destinatario) }}" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                    </div>

                    @if($nfe->cpf_destinatario)
                        <div>
                            <label class="block text-sm mb-1 font-medium text-green-700">CPF *</label>
                            <input type="text" name="cpf_destinatario" value="{{ old('cpf_destinatario', $nfe->cpf_destinatario) }}" class="w-full border-2 border-green-200 rounded-lg px-3 py-2 text-sm bg-green-50 text-gray-900" placeholder="000.000.000-00" maxlength="14">
                        </div>
                    @else
                        <div>
                            <label class="block text-sm mb-1 font-medium text-blue-700">CNPJ *</label>
                            <input type="text" name="cnpj_destinatario" value="{{ old('cnpj_destinatario', $nfe->cnpj_destinatario) }}" class="w-full border-2 border-blue-200 rounded-lg px-3 py-2 text-sm bg-blue-50 text-gray-900" placeholder="00.000.000/0000-00" maxlength="18">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1">Inscrição Estadual</label>
                                <input type="text" name="ie_destinatario" value="{{ old('ie_destinatario', $nfe->ie_destinatario) }}" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900">
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Indicador IE *</label>
                                <select name="indicador_ie_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                    <option value="1" {{ old('indicador_ie_destinatario', $nfe->indicador_ie_destinatario) == '1' ? 'selected' : '' }}>1 - Contribuinte ICMS</option>
                                    <option value="2" {{ old('indicador_ie_destinatario', $nfe->indicador_ie_destinatario) == '2' ? 'selected' : '' }}>2 - Contribuinte isento</option>
                                    <option value="9" {{ old('indicador_ie_destinatario', $nfe->indicador_ie_destinatario) == '9' ? 'selected' : '' }}>9 - Não contribuinte</option>
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">UF *</label>
                            <select name="uf_destinatario" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                                <option value="">Selecione...</option>
                                <option value="RJ" {{ old('uf_destinatario', $nfe->uf_destinatario) == 'RJ' ? 'selected' : '' }}>RJ - Rio de Janeiro</option>
                                <option value="SP" {{ old('uf_destinatario', $nfe->uf_destinatario) == 'SP' ? 'selected' : '' }}>SP - São Paulo</option>
                                <option value="PA" {{ old('uf_destinatario', $nfe->uf_destinatario) == 'PA' ? 'selected' : '' }}>PA - Pará</option>
                                <!-- Adicionar outros estados conforme necessário -->
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">CEP *</label>
                            <input type="text" name="cep_destinatario" value="{{ old('cep_destinatario', $nfe->cep_destinatario) }}" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Município *</label>
                            <input type="text" name="municipio_destinatario" value="{{ old('municipio_destinatario', $nfe->municipio_destinatario) }}" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Bairro *</label>
                            <input type="text" name="bairro_destinatario" value="{{ old('bairro_destinatario', $nfe->bairro_destinatario) }}" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Logradouro *</label>
                            <input type="text" name="logradouro_destinatario" value="{{ old('logradouro_destinatario', $nfe->logradouro_destinatario) }}" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Número *</label>
                            <input type="text" name="numero_destinatario" value="{{ old('numero_destinatario', $nfe->numero_destinatario) }}" class="w-full border rounded px-2 py-1 text-xs bg-gray-100 text-gray-900" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Botões de Ação -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="fas fa-cog text-gray-500"></i> Ações</h2>
                <div class="space-y-3">
                    <button type="submit" name="action" value="save" class="w-full px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-medium transition">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                    <button type="submit" name="action" value="emit" class="w-full px-4 py-2 rounded bg-green-600 hover:bg-green-700 text-white font-medium transition">
                        <i class="fas fa-paper-plane"></i> Salvar e Emitir
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
