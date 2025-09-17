# Relatório de Vendas por Usuário

## Descrição

O sistema de relatório de vendas permite gerar relatórios detalhados de vendas para usuários específicos, com dados organizados por período (semana, mês, ano) e por cliente. O relatório pode ser visualizado no navegador ou baixado em PDF.

## Funcionalidades

### 📊 Dados do Relatório
- **Total de vendas** no período selecionado
- **Número de vendas** realizadas
- **Ticket médio** por venda
- **Clientes atendidos** no período
- **Vendas por cliente** com valores e quantidades
- **Vendas por forma de pagamento** (à vista, a prazo)
- **Vendas por dia** no período selecionado

### 📅 Períodos Disponíveis
- **Semana Atual**: Segunda a domingo da semana atual
- **Mês Atual**: Primeiro ao último dia do mês atual
- **Ano Atual**: Janeiro a dezembro do ano atual

### 📄 Formatos de Saída
- **HTML**: Visualização no navegador com gráficos interativos
- **PDF**: Download para impressão e arquivamento

## Como Usar

### 1. Interface Web

1. Acesse **Administrativo > Relatório de Vendas** no menu lateral
2. Digite o email do usuário desejado
3. Selecione o período (semana, mês ou ano)
4. Escolha o formato (HTML ou PDF)
5. Clique em "Visualizar Relatório" ou "Baixar PDF"

### 2. Acesso Rápido para guabinorte1@gmail.com

Na página principal do relatório, há botões de acesso rápido para o usuário específico:
- **Semana (HTML/PDF)**
- **Mês (HTML/PDF)**
- **Ano (HTML/PDF)**

### 3. Linha de Comando

```bash
# Gerar relatório em PDF para guabinorte1@gmail.com (mês atual)
php artisan sales:report guabinorte1@gmail.com month --format=pdf

# Gerar relatório em HTML para qualquer usuário (semana atual)
php artisan sales:report usuario@email.com week --format=html

# Gerar relatório anual
php artisan sales:report guabinorte1@gmail.com year --format=pdf
```

### 4. API (para integrações)

```bash
# Buscar dados via API
GET /sales-reports/api/data?user_email=guabinorte1@gmail.com&period=month
```

## Estrutura do Relatório

### Resumo Executivo
- Total de vendas em R$
- Número total de transações
- Ticket médio por venda
- Número de clientes atendidos

### Vendas por Cliente
Tabela detalhada mostrando:
- Nome do cliente
- Total de vendas (R$)
- Número de vendas
- Ticket médio do cliente
- Percentual do total

### Análise por Forma de Pagamento
- Vendas à vista
- Vendas a prazo
- Percentuais de cada modalidade

### Vendas por Dia
Gráfico mostrando a evolução das vendas ao longo do período

## Arquivos do Sistema

### Controller
- `app/Http/Controllers/SalesReportController.php`

### Views
- `resources/views/sales_reports/index.blade.php` - Página principal
- `resources/views/sales_reports/user_report.blade.php` - Relatório HTML
- `resources/views/sales_reports/pdf/user_report.blade.php` - Template PDF

### Comando Artisan
- `app/Console/Commands/GenerateSalesReport.php`

### Rotas
```php
Route::prefix('sales-reports')->group(function () {
    Route::get('/', [SalesReportController::class, 'index'])->name('sales-reports.index');
    Route::post('/user', [SalesReportController::class, 'userSalesReport'])->name('sales-reports.user');
    Route::get('/guabinorte', [SalesReportController::class, 'guabinorteReport'])->name('sales-reports.guabinorte');
    Route::get('/api/data', [SalesReportController::class, 'getSalesDataApi'])->name('sales-reports.api.data');
});
```

## Exemplo de Uso

### Para o usuário guabinorte1@gmail.com

1. **Acesso via menu**: Administrativo > Relatório de Vendas
2. **Acesso direto**: `/sales-reports/guabinorte?period=month&format=pdf`
3. **Linha de comando**: `php artisan sales:report guabinorte1@gmail.com month --format=pdf`

### URLs Disponíveis

- `/sales-reports` - Página principal
- `/sales-reports/guabinorte?period=week&format=html` - Relatório da semana (HTML)
- `/sales-reports/guabinorte?period=month&format=pdf` - Relatório do mês (PDF)
- `/sales-reports/guabinorte?period=year&format=html` - Relatório do ano (HTML)

## Dependências

- **Laravel 12+**
- **Barryvdh DomPDF** - Geração de PDFs
- **Chart.js** - Gráficos interativos (HTML)
- **Carbon** - Manipulação de datas

## Segurança

- Middleware de autenticação obrigatório
- Verificação de acesso por empresa (`company.access`)
- Validação de email do usuário
- Sanitização de dados de entrada

## Troubleshooting

### Problema: Usuário não encontrado
**Solução**: Verifique se o email está correto e se o usuário existe no sistema.

### Problema: Nenhuma venda encontrada
**Solução**: Verifique se existem vendas no período selecionado e se o usuário tem vendas associadas.

### Problema: Erro ao gerar PDF
**Solução**: Verifique se a biblioteca DomPDF está instalada: `composer require barryvdh/laravel-dompdf`

### Problema: Gráficos não aparecem
**Solução**: Verifique se o Chart.js está carregado e se há dados suficientes para exibir os gráficos.

## Melhorias Futuras

1. **Filtros avançados**: Por vendedor, categoria de produto, etc.
2. **Comparação de períodos**: Comparar com período anterior
3. **Exportação Excel**: Além de PDF
4. **Agendamento**: Relatórios automáticos por email
5. **Dashboard**: Gráficos em tempo real
6. **Metas**: Comparação com metas estabelecidas
