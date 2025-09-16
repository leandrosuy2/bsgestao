# 🚀 Deploy Checklist - Sistema PDV + SaaS Admin

## ✅ Pré-Deploy
- [ ] Backup da base de dados criado
- [ ] Arquivos atualizados copiados
- [ ] Dependências instaladas (`composer install`)

## ✅ Migrations & Database
```bash
# Verificar migrations pendentes
php artisan migrate:status

# Executar migrations (apenas as novas)
php artisan migrate --force

# Limpar caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## ✅ Funcionalidades a Testar

### 1. Sistema PDV
- [ ] Abrir PDV (`/pdv/full`)
- [ ] Adicionar produtos
- [ ] Finalizar venda à vista
- [ ] Finalizar venda a prazo
- [ ] Geração automática de romaneio
- [ ] Status correto no romaneio (pago/a prazo)

### 2. Sistema de Usuários
- [ ] Listar usuários (`/users`)
- [ ] Criar usuário (`/users/create`)
- [ ] Editar usuário (`/users/{id}/edit`)
- [ ] Roles limitados (máximo 10)

### 3. Admin SaaS (apenas userId = 1)
- [ ] Menu "Empresas do Sistema" visível apenas para ID=1
- [ ] Acesso a `/admin/companies` restrito
- [ ] Gerenciamento de empresas funcional

### 4. Middleware & Segurança
- [ ] SuperAdmin middleware funcionando
- [ ] Usuários normais não veem menu admin
- [ ] Tentativa de acesso direto à /admin/companies retorna 403

## 🔧 Arquivos Alterados/Criados

### Controllers
- ✅ `PDVController.php` - Sistema completo PDV + romaneios
- ✅ `UserController.php` - Correção roles + create/edit
- ✅ `CompanyController.php` - Admin SaaS empresas

### Middleware
- ✅ `SuperAdminMiddleware.php` - Novo middleware ID=1

### Views
- ✅ `users/create.blade.php` - Layout corrigido
- ✅ `users/edit.blade.php` - Roles limitados
- ✅ `dashboard/layout.blade.php` - Menu admin condicional

### Config
- ✅ `bootstrap/app.php` - Middleware registrado
- ✅ `routes/web.php` - Rotas protegidas

## 🚨 Problemas Conhecidos Resolvidos
- ✅ Erro "Undefined variable $roles" corrigido
- ✅ View "layouts.dashboard not found" corrigido  
- ✅ Status romaneio sempre "pago" corrigido
- ✅ Data vencimento -1 dia corrigida
- ✅ Muitos roles carregando corrigido

## 🛠️ Comandos de Emergência
```bash
# Se algo der errado, restaurar backup
mysql -u usuario -p nome_do_banco < backup_antes_do_update.sql

# Recriar caches
php artisan optimize
php artisan config:cache
php artisan route:cache

# Verificar logs de erro
tail -f storage/logs/laravel.log
```

## 📋 URLs de Teste
- Dashboard: `/dashboard`
- PDV: `/pdv/full` 
- Usuários: `/users`
- Admin Empresas: `/admin/companies` (só ID=1)
- Login: `/login`

## 🎯 Resultado Esperado
- ✅ PDV totalmente funcional
- ✅ Romaneios automáticos com status correto
- ✅ Pagamento a prazo funcionando
- ✅ Admin SaaS restrito ao super usuário
- ✅ Interface limpa e responsiva
- ✅ Sem erros 500 ou views não encontradas
