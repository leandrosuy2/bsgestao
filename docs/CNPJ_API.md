# Funcionalidade de Busca de CNPJ

## Descrição

O sistema BSEstoque agora possui uma funcionalidade integrada para buscar automaticamente os dados de empresas através do CNPJ. Esta funcionalidade está disponível na tela de cadastro de novas empresas.

## Como Funciona

### 1. Interface do Usuário

Na tela de cadastro de empresa (`/admin/companies/create`), o campo CNPJ possui:

- **Máscara automática**: Formata o CNPJ no padrão XX.XXX.XXX/XXXX-XX
- **Botão de busca**: Ícone de lupa para buscar dados manualmente
- **Busca automática**: Busca dados quando o CNPJ está completo e o campo perde o foco
- **Validação**: Verifica se o CNPJ é válido antes de fazer a busca

### 2. API Utilizada

O sistema utiliza a API real da Receita Federal:

#### ReceitaWS
- **URL**: `https://www.receitaws.com.br/v1/cnpj/{cnpj}`
- **Vantagens**: Dados reais e atualizados da Receita Federal
- **Dados retornados**: Informações completas da empresa incluindo atividades, Simples Nacional, etc.
- **Formato**: CNPJ deve ser enviado sem pontos (apenas números)

### 3. Dados Preenchidos Automaticamente

Quando a busca é bem-sucedida, os seguintes campos são preenchidos:

- **Nome da Empresa**: Razão social
- **E-mail Principal**: E-mail cadastrado (se disponível)
- **Telefone**: Telefone principal
- **Endereço**: Logradouro
- **Número**: Número do endereço
- **Bairro**: Bairro
- **Cidade**: Município
- **Estado**: UF
- **CEP**: CEP

### 4. Informações Adicionais

Além dos campos do formulário, são exibidas informações adicionais da empresa:

- **Nome Fantasia**: Nome fantasia da empresa
- **Situação**: Situação cadastral (ATIVA, SUSPENSA, etc.)
- **Tipo**: Matriz ou Filial
- **Porte**: Porte da empresa
- **Data de Abertura**: Data de início das atividades
- **Capital Social**: Capital social da empresa
- **Natureza Jurídica**: Tipo de empresa (MEI, EIRELI, LTDA, etc.)
- **Simples Nacional**: Se a empresa é optante do Simples
- **Atividade Principal**: Código e descrição da atividade principal
- **Atividades Secundárias**: Lista de atividades secundárias

## Endpoints da API

### Buscar dados do CNPJ
```
GET /api/cnpj/{cnpj}
```

**Parâmetros:**
- `cnpj`: CNPJ da empresa (apenas números)

**Resposta de sucesso (200):**
```json
{
    "nome": "EMPRESA EXEMPLO LTDA",
    "email": "contato@empresa.com",
    "telefone": "(11) 99999-9999",
    "logradouro": "Rua das Flores",
    "numero": "123",
    "bairro": "Centro",
    "municipio": "São Paulo",
    "uf": "SP",
    "cep": "01234-567",
    "situacao": "ATIVA",
    "tipo": "MATRIZ",
    "porte": "EMPRESA DE PEQUENO PORTE",
    "abertura": "01/01/2020",
    "fantasia": "EMPRESA EXEMPLO",
    "capital_social": "100000.00"
}
```

**Respostas de erro:**
- `400`: CNPJ inválido
- `404`: CNPJ não encontrado
- `500`: Erro interno do servidor

## Validação de CNPJ

O sistema implementa validação completa de CNPJ:

1. **Verificação de formato**: Deve ter exatamente 14 dígitos
2. **Verificação de dígitos iguais**: Não pode ter todos os dígitos iguais
3. **Validação dos dígitos verificadores**: Algoritmo oficial da Receita Federal

## Arquivos do Sistema

### Controller
- `app/Http/Controllers/Api/CnpjController.php`: Controller da API

### View
- `resources/views/admin/companies/create.blade.php`: Interface com JavaScript

### Rotas
- `routes/web.php`: Rota da API `/api/cnpj/{cnpj}`

## Limitações

1. **Rate Limiting**: A API pode ter limitações de requisições
2. **Disponibilidade**: Depende da disponibilidade da API da Receita Federal
3. **Dados incompletos**: Nem todos os CNPJs possuem todos os dados preenchidos
4. **Atualização**: Os dados podem não estar 100% atualizados
5. **Formato**: CNPJ deve ser enviado sem pontos (apenas números)

## Alternativas

Para casos onde a ReceitaWS não estiver disponível, você pode:

1. **Serpro**: Solicitar credenciais e integrar com a API oficial
2. **BrasilAPI**: Usar a API pública gratuita (mais estável)
3. **Cache**: Implementar cache para evitar consultas repetidas

## Melhorias Futuras

1. **Cache**: Implementar cache para evitar consultas repetidas
2. **Múltiplas APIs**: Adicionar mais fontes de dados como fallback
3. **Validação em tempo real**: Validar CNPJ enquanto o usuário digita
4. **Histórico**: Manter histórico de consultas realizadas
5. **Notificações**: Alertar sobre CNPJs com situação irregular

## Troubleshooting

### Erro "CNPJ inválido"
- Verificar se o CNPJ tem 14 dígitos
- Verificar se os dígitos verificadores estão corretos
- O CNPJ deve ser enviado sem pontos (apenas números)

### Erro "CNPJ não encontrado"
- Verificar se o CNPJ está correto
- Verificar se a empresa está ativa na Receita Federal
- Tentar novamente em alguns minutos

### Erro "Erro ao consultar CNPJ"
- Verificar conexão com a internet
- Verificar se a API da Receita Federal está funcionando
- Tentar novamente mais tarde

### Dados incompletos
- Alguns CNPJs podem não ter todos os dados preenchidos
- Verificar manualmente os dados preenchidos
- Completar os campos faltantes manualmente 
