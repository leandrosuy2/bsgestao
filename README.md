# 🏢 BSEstoque - Sistema de Gestão Empresarial

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](https://opensource.org/licenses/MIT)

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

## 📋 Índice

- [Sobre o Projeto](#sobre-o-projeto)
- [Funcionalidades](#funcionalidades)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Estrutura do Sistema](#estrutura-do-sistema)
- [Módulos](#módulos)
- [Contribuição](#contribuição)
- [Licença](#licença)

## 🎯 Sobre o Projeto

O **BSEstoque** é um sistema completo de gestão empresarial desenvolvido em Laravel 12, que integra controle de estoque, gestão financeira e recursos humanos em uma única plataforma moderna e intuitiva.

### 🎨 Características Principais

- **Interface Moderna**: Design responsivo com Tailwind CSS
- **Módulos Integrados**: Estoque, Financeiro e RH/DP
- **Dashboard Interativo**: Gráficos e estatísticas em tempo real
- **Sistema de Usuários**: Controle de acesso e permissões
- **Relatórios Avançados**: Geração de relatórios detalhados
- **Responsivo**: Funciona perfeitamente em desktop, tablet e mobile

## 🚀 Funcionalidades

### 📦 Módulo de Estoque
- **Gestão de Produtos**: Cadastro, edição e exclusão de produtos
- **Categorização**: Organização por categorias com códigos únicos
- **Controle de Estoque**: Acompanhamento de quantidade em tempo real
- **Movimentações**: Registro de entradas, saídas e transferências
- **Alertas**: Notificações de estoque mínimo
- **Relatórios**: Histórico de movimentações e produtos mais movimentados

### 💰 Módulo Financeiro
- **Contas a Pagar**: Gestão completa de obrigações
- **Contas a Receber**: Controle de recebimentos
- **Relatórios Financeiros**: Dashboard com indicadores financeiros
- **Filtros Avançados**: Busca por período, status e valores
- **Status de Pagamento**: Acompanhamento de vencimentos

### 👥 Módulo de RH/DP
- **Controle de Ponto**: Registro de entrada e saída
- **Folha de Pagamento**: Gestão de salários e benefícios
- **Férias**: Controle de períodos de férias
- **Licenças**: Gestão de licenças médicas e pessoais
- **Benefícios**: Cadastro e controle de benefícios
- **Holerites**: Geração de contracheques

### 📊 Dashboard
- **Cards Informativos**: Resumo dos principais indicadores
- **Gráficos Interativos**: Visualização de dados com Chart.js
- **Estatísticas em Tempo Real**: Atualização automática de dados
- **Navegação Intuitiva**: Menu lateral responsivo

## 🛠 Tecnologias Utilizadas

### Backend
- **Laravel 12**: Framework PHP moderno e robusto
- **PHP 8.2+**: Linguagem de programação
- **MySQL 8.0+**: Banco de dados relacional
- **Eloquent ORM**: Mapeamento objeto-relacional

### Frontend
- **Tailwind CSS 3.x**: Framework CSS utilitário
- **Alpine.js**: Framework JavaScript minimalista
- **Chart.js**: Biblioteca para gráficos
- **Heroicons**: Ícones SVG modernos

### Ferramentas
- **Composer**: Gerenciador de dependências PHP
- **Artisan**: CLI do Laravel
- **Git**: Controle de versão

## 📦 Instalação

### Pré-requisitos
- PHP 8.2 ou superior
- Composer
- MySQL 8.0 ou superior
- Node.js e NPM (para assets)

### Passos para Instalação

1. **Clone o repositório**
```bash
git clone https://github.com/leandrosuy2/bsestoque.git
cd bsestoque
```

2. **Instale as dependências PHP**
```bash
composer install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
```bash
# Edite o arquivo .env com suas configurações de banco
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bsestoque
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

5. **Execute as migrations**
```bash
php artisan migrate
```

6. **Popule o banco com dados de exemplo**
```bash
php artisan db:seed
```

7. **Instale e compile os assets**
```bash
npm install
npm run dev
```

8. **Inicie o servidor**
```bash
php artisan serve
```

O sistema estará disponível em: `http://localhost:8000`

## ⚙️ Configuração

### Usuário Padrão
- **Email**: admin@bsestoque.com
- **Senha**: password

### Configurações Adicionais
- Configure o arquivo `.env` com suas credenciais de banco
- Ajuste as configurações de email se necessário
- Configure o cache e sessões conforme sua necessidade

## 📁 Estrutura do Sistema

```
BSEstoque/
├── app/
│   ├── Http/Controllers/     # Controllers dos módulos
│   ├── Models/              # Modelos Eloquent
│   └── Providers/           # Service Providers
├── database/
│   ├── migrations/          # Migrations do banco
│   ├── seeders/            # Seeders com dados de exemplo
│   └── factories/          # Factories para testes
├── resources/
│   └── views/              # Views Blade organizadas por módulo
├── routes/
│   └── web.php            # Rotas da aplicação
└── public/                # Assets públicos
```

## 🎯 Módulos Detalhados

### 📦 Estoque
- **Produtos**: Gestão completa de produtos com categorias
- **Movimentações**: Controle de entrada, saída e transferências
- **Relatórios**: Alertas de estoque e histórico de movimentações

### 💰 Financeiro
- **Contas a Pagar**: Gestão de obrigações com vencimentos
- **Contas a Receber**: Controle de recebimentos
- **Relatórios**: Dashboard financeiro com indicadores

### 👥 RH/DP
- **Funcionários**: Cadastro e gestão de colaboradores
- **Controle de Ponto**: Registro de entrada e saída
- **Folha de Pagamento**: Gestão de salários
- **Férias e Licenças**: Controle de períodos
- **Benefícios**: Gestão de benefícios corporativos
- **Holerites**: Geração de contracheques

## 🤝 Contribuição

Contribuições são sempre bem-vindas! Para contribuir:

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

### Padrões de Código
- Siga os padrões PSR-12 para PHP
- Use nomes descritivos para variáveis e funções
- Documente funções complexas
- Mantenha a consistência com o código existente

## 📝 Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## 🔗 Links Úteis

- **Repositório**: [https://github.com/leandrosuy2/bsestoque](https://github.com/leandrosuy2/bsestoque)
- **Laravel**: [https://laravel.com](https://laravel.com)
- **Tailwind CSS**: [https://tailwindcss.com](https://tailwindcss.com)

## 📞 Suporte

Para suporte e dúvidas:
- Abra uma [issue](https://github.com/leandrosuy2/bsestoque/issues) no GitHub
- Entre em contato através do email do projeto

---

**Desenvolvido com ❤️ usando Laravel e Tailwind CSS** 
