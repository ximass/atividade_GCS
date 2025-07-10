## 1. Ambiente e Infraestrutura 1

### 1.1 Containerização
- **Docker**: Ambiente principal de execução
- **Docker Compose**: Orquestração de containers
- **Arquitetura Multi-Container**:
  - Container PHP-FPM (aplicação Laravel)
  - Container Nginx (servidor web)
  - Network bridge para comunicação entre containers

### 1.2 Ambientes Configurados
- **Desenvolvimento**: Local com Docker Compose
- **Staging**: Ambiente de homologação (porta 8081)
- **Production**: Ambiente de produção (porta 8080)

### 1.3 Sistema Operacional
- **Ubuntu**: Sistema base dos containers
- **PHP-FPM**: Baseado em PHP 8.3-FPM oficial
- **Nginx**: Última versão estável oficial

---

## 2. Stack de Tecnologias

### 2.1 Linguagem de Programação
- **PHP 8.3**: Linguagem principal
- **JavaScript**: Frontend (Vite + recursos)
- **HTML/CSS**: Interface de usuário
- **Blade**: Template engine do Laravel

### 2.2 Framework e Bibliotecas
- **Laravel 12.0**: Framework PHP principal
- **Composer**: Gerenciador de dependências PHP
- **Vite**: Build tool para assets frontend
- **TailwindCSS 4.0**: Framework CSS
- **Bootstrap 5.3**: Framework CSS (interface)

### 2.3 Dependências Principais
```json
{
  "barryvdh/laravel-dompdf": "^3.1",  // Geração de PDF
  "laravel/framework": "^12.0",      // Framework core
  "laravel/tinker": "^2.10.1"        // REPL
}
```

---

## 3. Banco de Dados

### 3.1 SGBDs Suportados
- **PostgreSQL**: Banco padrão
- **SQLite**: Banco para testes
- **Migrations**: Estruturação automática do banco
- **Seeders**: População inicial de dados

### 3.3 Estrutura Principal
```sql
-- Tabela de usuários (Laravel padrão)
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Tabela de tarefas (customizada)
CREATE TABLE tarefa (
    id BIGINT PRIMARY KEY,
    descricao TEXT,
    data_criacao DATE,
    data_prevista DATE,
    data_encerramento DATE,
    situacao VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 4. Controle de Versão

### 4.1 Git e GitHub
- **Repositório**: https://github.com/ximass/atividade_GCS.git
- **Branches principais**:
  - `master`: Produção
  - `staging`: Homologação
  - Feature branches para desenvolvimento

### 4.2 Estratégia de Branching
- **GitFlow**: Fluxo de trabalho baseado em branches
- **Pull Requests**: Revisão de código obrigatória
- **CI/CD**: Integração contínua com GitHub Actions

---

## 5. Testes e Qualidade

### 5.1 Framework de Testes
- **PHPUnit 11.5**: Framework principal de testes
- **Laravel Testing**: Recursos integrados do Laravel

### 5.2 Tipos de Testes
- **Unit Tests**: Testes unitários das classes
- **Cobertura**: Testes do TarefaController

---

## 6. CI/CD e Deploy

### 6.1 GitHub Actions
- **Workflow**: `.github/workflows/laravel.yml`
- **Trigger**: Pull requests para staging/master
- **Runner**: Ubuntu Latest

### 6.2 Pipeline de CI/CD
```yaml
steps:
  - Setup PHP 8.3
  - Checkout código
  - Instalar dependências
  - Configurar ambiente
  - Executar testes
  - Deploy (se aprovado)
```

### 6.3 Deploy Automatizado
- **Dockerfile**: Build da imagem
- **Args de Build**: Repositório e branch dinâmicos
- **Volumes**: Mapeamento de arquivos
- **Networks**: Isolamento de containers

---

## 7. Arquitetura da Aplicação

### 7.1 Padrão MVC
- **Models**: Representação de dados
- **Views**: Interface de usuário (Blade)
- **Controllers**: Lógica de negócio

### 7.2 Estrutura de Diretórios
```
app/
├── Http/Controllers/     # Controladores
├── Models/              # Modelos de dados
└── Providers/           # Provedores de serviços

resources/
├── views/              # Templates Blade
├── css/                # Estilos
└── js/                 # Scripts

database/
├── migrations/         # Migrações
├── seeders/           # Seeders
└── factories/         # Factories
```

### 7.3 Funcionalidades Principais
- **CRUD de Tarefas**: Criar, ler, atualizar, deletar
- **Filtros**: Por situação e data
- **Relatórios**: Exportação em PDF
- **Interface Responsiva**: Bootstrap + TailwindCSS

---

## 8. Configuração de Ambiente

### 8.1 Variáveis de Ambiente
- **APP_ENV**: ambiente atual
- **DB_CONNECTION**: tipo de banco
- **DB_DATABASE**: caminho do banco
- **APP_KEY**: chave de criptografia

### 8.2 Configurações por Ambiente
- **Development**: `.env`
- **Staging**: `.env.staging`
- **Production**: `.env.production`

### 8.3 Portas e Acessos
- **Development**: localhost:8000
- **Staging**: localhost:8081
- **Production**: localhost:8080

