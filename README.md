# Sistema de Tarefas Laravel

Sistema de gerenciamento de tarefas desenvolvido em Laravel com pipeline CI/CD automatizado.

## 🔗 Links

- **Produção**: http://177.44.248.74/tarefas
- **Staging**: http://177.44.248.74:8080/tarefas (quando ativo)

## 🚀 CI/CD Pipeline

Este projeto possui um pipeline CI/CD simplificado que automatiza:

- ✅ **Testes**: Executa `php artisan test` automaticamente
- ✅ **Migrations**: Atualiza banco de dados automaticamente  
- ✅ **Deploy**: Deploy automático via Docker containers
- ✅ **Environments**: Staging e Production separados

### Branches de Deploy

- **`staging`** → Deploy automático para ambiente de teste
- **`main/master`** → Deploy automático para produção

## 🛠️ Desenvolvimento

### Pré-requisitos

- PHP 8.3+
- Composer
- Docker & Docker Compose
- PostgreSQL

### Setup Local

1. **Clone o repositório**
   ```bash
   git clone [url-do-repo]
   cd atividade
   ```

2. **Instale dependências**
   ```bash
   composer install
   ```

3. **Configure ambiente**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Execute migrations**
   ```bash
   php artisan migrate
   ```

5. **Rode os testes**
   ```bash
   php artisan test
   ```

### Scripts Úteis

```bash
# Testa pipeline completo
.\scripts\test-pipeline.ps1

# Deploy local para staging
.\scripts\deploy-local.ps1 -Environment staging

# Deploy local para produção  
.\scripts\deploy-local.ps1 -Environment production
```

## 📁 Estrutura do Projeto

```
├── app/Http/Controllers/TarefaController.php  # Controller principal
├── resources/views/tarefas/                   # Views das tarefas
├── routes/web.php                            # Rotas web
├── tests/Unit/TarefaControllerTest.php       # Testes unitários
├── docker/                                   # Configurações Docker
│   ├── docker-compose.staging.yml
│   ├── docker-compose.prod.yml
│   └── Dockerfile
├── .github/workflows/                        # CI/CD Pipeline
│   ├── staging.yml
│   └── production.yml
└── scripts/                                  # Scripts auxiliares
    ├── test-pipeline.ps1
    ├── deploy-local.ps1
    └── backup-database.ps1
```

## 🐳 Docker

O projeto usa Docker para deployment:

- **Staging**: Porta 8080
- **Production**: Porta 80
- **PHP**: 8.3-fpm com extensões necessárias
- **Nginx**: Proxy reverso

## 📋 Funcionalidades

- ✅ CRUD completo de tarefas
- ✅ Filtros por data e situação
- ✅ Export para PDF
- ✅ Autenticação de usuários
- ✅ Interface responsiva com Bootstrap

## 🧪 Testes

```bash
# Todos os testes
php artisan test

# Testes específicos
php artisan test --filter TarefaControllerTest

# Com cobertura
./vendor/bin/phpunit --coverage-text
```

## 📚 Documentação Adicional

- [CI/CD Pipeline](CI-CD-README.md) - Documentação detalhada do pipeline
- [Scripts](scripts/README.md) - Guia dos scripts auxiliares
