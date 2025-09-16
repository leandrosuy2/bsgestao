# Sistema de Papéis e Permissões - BSEstoque

## Visão Geral

O BSEstoque implementa um sistema robusto de papéis e permissões que permite controlar o acesso dos usuários a diferentes funcionalidades do sistema. Cada empresa pode criar seus próprios papéis e atribuir permissões específicas, garantindo que cada usuário tenha acesso apenas às funcionalidades necessárias para seu trabalho.

## Estrutura do Sistema

### 1. Permissões (Permissions)
- **Definição**: Ações específicas que um usuário pode realizar no sistema
- **Organização**: Agrupadas por módulos (products, finance, hr, admin, etc.)
- **Exemplos**: `products.view`, `products.create`, `finance.reports.view`

### 2. Papéis (Roles)
- **Definição**: Conjunto de permissões que define as responsabilidades de um usuário
- **Escopo**: Cada empresa tem seus próprios papéis
- **Exemplos**: Estoquista, Financeiro, Administrativo

### 3. Usuários (Users)
- **Definição**: Pessoas que acessam o sistema
- **Atribuição**: Podem ter múltiplos papéis
- **Controle**: Status ativo/inativo

## Módulos de Permissões

### Produtos (`products`)
- `products.view` - Visualizar produtos
- `products.create` - Criar produtos
- `products.edit` - Editar produtos
- `products.delete` - Excluir produtos

### Categorias (`categories`)
- `categories.view` - Visualizar categorias
- `categories.create` - Criar categorias
- `categories.edit` - Editar categorias
- `categories.delete` - Excluir categorias

### Movimentações de Estoque (`stock_movements`)
- `stock_movements.view` - Visualizar movimentações
- `stock_movements.create` - Criar movimentações
- `stock_movements.edit` - Editar movimentações
- `stock_movements.delete` - Excluir movimentações

### Financeiro (`finance`)
- `payables.view` - Visualizar contas a pagar
- `payables.create` - Criar contas a pagar
- `payables.edit` - Editar contas a pagar
- `payables.delete` - Excluir contas a pagar
- `receivables.view` - Visualizar contas a receber
- `receivables.create` - Criar contas a receber
- `receivables.edit` - Editar contas a receber
- `receivables.delete` - Excluir contas a receber
- `financial_reports.view` - Visualizar relatórios financeiros

### Relatórios (`reports`)
- `reports.view` - Visualizar relatórios de estoque
- `reports.export` - Exportar relatórios

### Funcionários (`employees`)
- `employees.view` - Visualizar funcionários
- `employees.create` - Criar funcionários
- `employees.edit` - Editar funcionários
- `employees.delete` - Excluir funcionários

### RH (`hr`)
- `timeclocks.view` - Visualizar ponto eletrônico
- `timeclocks.create` - Registrar ponto
- `timeclocks.edit` - Editar registros de ponto
- `payrolls.view` - Visualizar folha de pagamento
- `payrolls.create` - Criar folha de pagamento
- `payrolls.edit` - Editar folha de pagamento
- `vacations.view` - Visualizar férias
- `vacations.create` - Criar solicitação de férias
- `vacations.edit` - Editar férias
- `leaves.view` - Visualizar licenças
- `leaves.create` - Criar solicitação de licença
- `leaves.edit` - Editar licenças
- `benefits.view` - Visualizar benefícios
- `benefits.create` - Criar benefícios
- `benefits.edit` - Editar benefícios
- `payslips.view` - Visualizar holerites
- `payslips.create` - Criar holerites
- `payslips.edit` - Editar holerites

### Administrativo (`admin`)
- `roles.view` - Visualizar papéis
- `roles.create` - Criar papéis
- `roles.edit` - Editar papéis
- `roles.delete` - Excluir papéis
- `users.view` - Visualizar usuários
- `users.create` - Criar usuários
- `users.edit` - Editar usuários
- `users.delete` - Excluir usuários

## Papéis Padrão

### 1. Estoquista
**Permissões:**
- Visualizar produtos
- Criar produtos
- Editar produtos
- Visualizar categorias
- Visualizar movimentações
- Criar movimentações
- Visualizar relatórios de estoque

### 2. Financeiro
**Permissões:**
- Visualizar contas a pagar
- Criar contas a pagar
- Editar contas a pagar
- Visualizar contas a receber
- Criar contas a receber
- Editar contas a receber
- Visualizar relatórios financeiros
- Visualizar relatórios de estoque

### 3. Administrativo
**Permissões:**
- Todas as permissões do Estoquista
- Todas as permissões do Financeiro
- Visualizar funcionários
- Criar funcionários
- Editar funcionários
- Visualizar papéis
- Criar papéis
- Editar papéis
- Visualizar usuários
- Criar usuários
- Editar usuários

## Como Usar

### 1. Criando um Novo Papel

1. Acesse **Administrativo > Papéis e Permissões**
2. Clique em **"Novo Papel"**
3. Preencha:
   - **Nome**: Nome do papel (ex: "Vendedor")
   - **Descrição**: Descrição das responsabilidades
4. Selecione as permissões desejadas
5. Clique em **"Criar Papel"**

### 2. Criando um Novo Usuário

1. Acesse **Administrativo > Usuários**
2. Clique em **"Novo Usuário"**
3. Preencha:
   - **Nome**: Nome completo
   - **Email**: Email do usuário
   - **Senha**: Senha inicial
4. Selecione os papéis desejados
5. Clique em **"Criar Usuário"**

### 3. Editando Permissões de um Papel

1. Acesse **Administrativo > Papéis e Permissões**
2. Clique em **"Editar"** no papel desejado
3. Marque/desmarque as permissões
4. Clique em **"Atualizar Papel"**

### 4. Atribuindo Papéis a Usuários

1. Acesse **Administrativo > Usuários**
2. Clique em **"Editar"** no usuário desejado
3. Selecione os papéis desejados
4. Clique em **"Atualizar Usuário"**

## Controles de Acesso

### 1. No Menu
O sistema automaticamente oculta itens de menu baseado nas permissões do usuário:
- Seções inteiras são ocultadas se o usuário não tem permissão para nenhum módulo
- Itens individuais são ocultados se o usuário não tem a permissão específica

### 2. Nas Views
Use as diretivas Blade para controlar o acesso:

```blade
@can('products.create')
    <a href="/products/create" class="btn">Criar Produto</a>
@endcan

@canAny(['products.edit', 'products.delete'])
    <div class="actions">
        @can('products.edit')
            <a href="/products/{{ $product->id }}/edit">Editar</a>
        @endcan
        @can('products.delete')
            <form action="/products/{{ $product->id }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit">Excluir</button>
            </form>
        @endcan
    </div>
@endcanAny
```

### 3. Nos Controllers
Use o middleware `CheckPermission` nas rotas:

```php
Route::middleware(['auth', 'check.permission:products.view'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
});

Route::middleware(['auth', 'check.permission:products.create'])->group(function () {
    Route::get('/products/create', [ProductController::class, 'create']);
    Route::post('/products', [ProductController::class, 'store']);
});
```

### 4. Verificações Programáticas
No código PHP, use os métodos do modelo User:

```php
// Verificar uma permissão
if ($user->hasPermission('products.create')) {
    // Usuário pode criar produtos
}

// Verificar múltiplas permissões (OR)
if ($user->hasAnyPermission(['products.edit', 'products.delete'])) {
    // Usuário pode editar OU excluir produtos
}

// Verificar múltiplas permissões (AND)
if ($user->hasAllPermissions(['products.view', 'products.create'])) {
    // Usuário pode visualizar E criar produtos
}

// Obter todas as permissões
$permissions = $user->getAllPermissions();
```

## Diretivas Blade Disponíveis

### @can
Verifica se o usuário tem uma permissão específica:
```blade
@can('products.create')
    <!-- Conteúdo visível apenas para quem pode criar produtos -->
@endcan
```

### @canAny
Verifica se o usuário tem qualquer uma das permissões listadas:
```blade
@canAny(['products.edit', 'products.delete'])
    <!-- Conteúdo visível para quem pode editar OU excluir produtos -->
@endcanAny
```

### @canAll
Verifica se o usuário tem todas as permissões listadas:
```blade
@canAll(['products.view', 'products.create'])
    <!-- Conteúdo visível apenas para quem pode visualizar E criar produtos -->
@endcanAll
```

### @canModule
Verifica se o usuário tem permissão para qualquer funcionalidade do módulo:
```blade
@canModule('products')
    <!-- Seção visível apenas para quem tem permissão no módulo produtos -->
@endcanModule
```

## Boas Práticas

### 1. Princípio do Menor Privilégio
- Atribua apenas as permissões necessárias para cada papel
- Evite dar permissões excessivas

### 2. Nomenclatura Consistente
- Use nomes descritivos para papéis
- Mantenha padrão nas permissões: `modulo.acao`

### 3. Revisão Regular
- Revise periodicamente as permissões dos usuários
- Remova permissões desnecessárias

### 4. Documentação
- Mantenha documentação dos papéis e suas responsabilidades
- Treine usuários sobre as permissões

## Segurança

### 1. Validação no Backend
- Sempre valide permissões no servidor
- Não confie apenas em controles de interface

### 2. Middleware
- Use middleware para proteger rotas
- Implemente verificações em todos os endpoints

### 3. Auditoria
- Mantenha logs de alterações de permissões
- Monitore acessos a funcionalidades sensíveis

## Troubleshooting

### Problema: Usuário não vê itens no menu
**Solução:** Verifique se o usuário tem as permissões necessárias atribuídas aos seus papéis.

### Problema: Erro 403 ao acessar funcionalidade
**Solução:** Verifique se a rota está protegida pelo middleware correto e se o usuário tem a permissão necessária.

### Problema: Papel não aparece na lista
**Solução:** Verifique se o papel está ativo e se pertence à empresa correta.

### Problema: Permissões não são aplicadas
**Solução:** Limpe o cache da aplicação: `php artisan cache:clear` 
