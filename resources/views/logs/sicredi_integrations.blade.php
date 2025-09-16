
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integração Sicredi por Usuário</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <div class="container mx-auto px-6 py-8">
        <h1 class="text-2xl font-bold mb-2 flex items-center">
            <i class="fas fa-university text-green-400 mr-2"></i> Integração Sicredi por Usuário
        </h1>
        <p class="text-gray-400 mb-6">Ative, desative e configure credenciais Sicredi para cada usuário do sistema.</p>
        @if(session('success'))
            <div class="bg-green-600 text-white p-4 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full bg-gray-800 rounded shadow">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Usuário</th>
                        <th class="px-4 py-2">E-mail</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">x-api-key</th>
                        <th class="px-4 py-2">Client ID</th>
                        <th class="px-4 py-2">Client Secret</th>
                        <th class="px-4 py-2">Cooperativa</th>
                        <th class="px-4 py-2">Posto</th>
                        <th class="px-4 py-2">Cod. Beneficiário</th>
                        <th class="px-4 py-2">Nome Beneficiário</th>
                        <th class="px-4 py-2">Doc. Beneficiário</th>
                        <th class="px-4 py-2">Tipo Pessoa</th>
                        <th class="px-4 py-2">CEP</th>
                        <th class="px-4 py-2">Cidade</th>
                        <th class="px-4 py-2">UF</th>
                        <th class="px-4 py-2">Endereço</th>
                        <th class="px-4 py-2">Número</th>
                        <th class="px-4 py-2">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-b border-gray-700">
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            @if($user->paymentIntegration && $user->paymentIntegration->enabled)
                                <span class="text-green-400 font-bold">Ativo</span>
                            @else
                                <span class="text-red-400 font-bold">Inativo</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->x_api_key ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->client_id ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->client_secret ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->cooperativa ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->posto ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->codigo_beneficiario ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->beneficiario_nome ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->beneficiario_documento ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->beneficiario_tipo_pessoa ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->beneficiario_cep ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->beneficiario_cidade ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->beneficiario_uf ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->beneficiario_endereco ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $user->paymentIntegration->beneficiario_numero ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <form method="POST" action="{{ route('logs.sicredi-integrations.toggle', $user->id) }}" class="grid grid-cols-1 gap-1">
                                @csrf
                                <input type="hidden" name="enabled" value="{{ $user->paymentIntegration && $user->paymentIntegration->enabled ? 0 : 1 }}">
                                <input type="text" name="x_api_key" value="{{ $user->paymentIntegration->x_api_key ?? '' }}" placeholder="x-api-key" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="client_id" value="{{ $user->paymentIntegration->client_id ?? '' }}" placeholder="Client ID" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="client_secret" value="{{ $user->paymentIntegration->client_secret ?? '' }}" placeholder="Client Secret" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="cooperativa" value="{{ $user->paymentIntegration->cooperativa ?? '' }}" placeholder="Cooperativa" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="posto" value="{{ $user->paymentIntegration->posto ?? '' }}" placeholder="Posto" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="codigo_beneficiario" value="{{ $user->paymentIntegration->codigo_beneficiario ?? '' }}" placeholder="Cod. Beneficiário" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="beneficiario_nome" value="{{ $user->paymentIntegration->beneficiario_nome ?? '' }}" placeholder="Nome Beneficiário" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="beneficiario_documento" value="{{ $user->paymentIntegration->beneficiario_documento ?? '' }}" placeholder="Doc. Beneficiário" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="beneficiario_tipo_pessoa" value="{{ $user->paymentIntegration->beneficiario_tipo_pessoa ?? '' }}" placeholder="Tipo Pessoa" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="beneficiario_cep" value="{{ $user->paymentIntegration->beneficiario_cep ?? '' }}" placeholder="CEP" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="beneficiario_cidade" value="{{ $user->paymentIntegration->beneficiario_cidade ?? '' }}" placeholder="Cidade" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="beneficiario_uf" value="{{ $user->paymentIntegration->beneficiario_uf ?? '' }}" placeholder="UF" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="beneficiario_endereco" value="{{ $user->paymentIntegration->beneficiario_endereco ?? '' }}" placeholder="Endereço" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <input type="text" name="beneficiario_numero" value="{{ $user->paymentIntegration->beneficiario_numero ?? '' }}" placeholder="Número" class="border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                                <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white text-xs mt-2">
                                    {{ $user->paymentIntegration && $user->paymentIntegration->enabled ? 'Desativar' : 'Ativar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulários de Teste Sicredi -->
    <div class="container mx-auto mt-10 bg-gray-800 rounded-lg p-6 shadow-lg">
        <h2 class="text-xl font-bold mb-4 flex items-center"><i class="fas fa-vial text-green-400 mr-2"></i> Testes de Integração Sicredi</h2>
        @if(session('sicredi_result'))
            <div class="bg-blue-700 text-white p-4 rounded-lg mb-4">
                <pre class="whitespace-pre-wrap">{{ session('sicredi_result') }}</pre>
            </div>
        @endif
        <form method="POST" action="{{ route('logs.sicredi-integrations.test') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="block text-sm mb-1">Usuário</label>
                <select name="user_id" class="w-full border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Ação</label>
                <select name="action" class="w-full border rounded px-2 py-1 text-xs bg-gray-900 text-white">
                    <option value="auth">Autenticar</option>
                    <option value="criar">Criar Boleto</option>
                    <option value="consultar">Consultar Boleto</option>
                    <option value="pdf">Baixar PDF</option>
                    <option value="baixa">Baixar (liquidar)</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">Nosso Número (para consultar/pdf/baixa)</label>
                <input type="text" name="nosso_numero" class="w-full border rounded px-2 py-1 text-xs bg-gray-900 text-white" placeholder="Opcional para criar/autenticar">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">Dados do Boleto (JSON, para criar)</label>
                <textarea name="boleto_data" rows="3" class="w-full border rounded px-2 py-1 text-xs bg-gray-900 text-white" placeholder='Ex: {"valor":100.00, ...}'></textarea>
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white font-bold">Executar Teste</button>
            </div>
        </form>
    </div>
</body>
</html>
