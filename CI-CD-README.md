# CI/CD Pipeline para Laravel

Este projeto utiliza GitHub Actions para implementar um pipeline de CI/CD automatizado que executa testes e deploy nas branches `staging` e `master/main`.

## Workflows Configurados

### 1. Test Suite (`tests.yml`)
- **Trigger**: Pull Requests para `staging`, `master`, `main`, `develop`
- **Função**: Executa testes automatizados, verificações de código e auditoria de segurança
- **Não executa deploy**

### 2. Staging Deployment (`staging.yml`)
- **Trigger**: Push/merge para branch `staging`
- **Função**: 
  - Executa todos os testes
  - Cria backup automático do banco
  - Faz deploy para ambiente de staging
  - Executa migrations
  - Executa seeds (opcional)
  - Verifica saúde da aplicação com retry
  - Implementa rollback automático em falhas

### 3. Production Deployment (`production.yml`)
- **Trigger**: Push/merge para branch `master` ou `main`
- **Função**:
  - Executa todos os testes
  - Cria backup automático do banco
  - Otimiza aplicação para produção
  - Faz deploy para ambiente de produção
  - Executa migrations
  - Limpa e otimiza cache
  - Verifica saúde da aplicação com múltiplas tentativas
  - Executa smoke tests
  - Implementa rollback automático em falhas

### 4. Security and Dependencies Check (`security.yml`)
- **Trigger**: 
  - Schedule: Toda segunda-feira às 6h
  - Push para branches principais
  - Pull Requests
- **Função**:
  - Auditoria de segurança com Composer
  - Verificação de dependências desatualizadas
  - Verificação de qualidade de código
  - Scan de segurança do Docker com Trivy

### 5. Post-Deployment Monitoring (`monitoring.yml`)
- **Trigger**: Após conclusão dos workflows de deploy
- **Função**:
  - Testes de performance automatizados
  - Verificação de headers de segurança
  - Testes de carga básicos
  - Monitoramento de tempos de resposta
  - Alertas automáticos em caso de falha

## Configuração de Secrets

Para o funcionamento completo do pipeline, configure os seguintes secrets no GitHub:

### Obrigatórios
```
DB_PASSWORD           # Senha do banco PostgreSQL
```

### Opcionais
```
DOCKER_USERNAME       # Usuário do Docker Hub
DOCKER_PASSWORD       # Senha/Token do Docker Hub
SLACK_WEBHOOK_URL     # Webhook para notificações Slack
DISCORD_WEBHOOK_URL   # Webhook para notificações Discord
NEW_RELIC_LICENSE_KEY # Chave de licença New Relic
DATADOG_API_KEY       # Chave API Datadog
```

## Scripts Auxiliares

### Backup de Banco de Dados
- **Linux/Mac**: `scripts/backup-database.sh`
- **Windows**: `scripts/backup-database.ps1`
- **Uso**: Executado automaticamente antes dos deploys
- **Retenção**: Mantém os últimos 5 backups

### Comandos Composer Adicionais
```bash
composer test            # Executa testes Laravel
composer test-coverage   # Executa testes com cobertura
composer test-ci         # Pipeline completo de CI
composer style           # Corrige estilo de código
composer style-check     # Verifica estilo de código
composer security        # Auditoria de segurança
composer prepare-staging # Prepara ambiente staging
composer prepare-production # Prepara ambiente produção
```

## Ambientes

### Testing
- **Banco**: PostgreSQL temporário
- **Cache**: Array (em memória)
- **Sessions**: Array (em memória)
- **Mail**: Array (simulado)

### Staging
- **Porta**: 8080
- **Banco**: PostgreSQL configurado em `.env.staging`
- **Docker Compose**: `docker-compose.staging.yml`

### Production
- **Porta**: 80
- **Banco**: PostgreSQL configurado em `.env.production`
- **Docker Compose**: `docker-compose.prod.yml`
- **Otimizações**: Config cache, Route cache, View cache ativados

## Health Check

A aplicação possui uma rota de health check em `/health` que retorna:

```json
{
  "status": "OK",
  "timestamp": "2025-06-11T10:30:00.000000Z",
  "environment": "production"
}
```

## Fluxo de Trabalho Recomendado

1. **Desenvolvimento**: Trabalhe em feature branches
2. **Teste**: Abra PR para `develop` - executa apenas testes
3. **Staging**: Merge para `staging` - executa testes + deploy staging
4. **Produção**: Merge para `master/main` - executa testes + deploy produção

## Comandos de Teste Local

```bash
# Executar testes
php artisan test

# Executar PHPUnit
./vendor/bin/phpunit

# Verificar estilo de código
./vendor/bin/pint --test

# Auditoria de segurança
composer audit
```

## Estrutura Docker

- **Dockerfile**: Configura ambiente PHP 8.3 com extensões necessárias
- **docker-compose.staging.yml**: Configuração para ambiente de staging
- **docker-compose.prod.yml**: Configuração para ambiente de produção
- **nginx**: Configurações específicas para cada ambiente

## Migrations e Seeds

- **Migrations**: Executadas automaticamente em todos os ambientes
- **Seeds**: Executadas apenas em staging (opcional em produção)
- **Rollback**: Implementado em caso de falha no deploy de produção

## Monitoramento

- Health checks automáticos após deploy
- Logs de deploy disponíveis no GitHub Actions
- Notificações de falha configuradas

## Funcionalidades Avançadas

### Backup Automático
- Backup automático do banco antes de cada deploy
- Compressão dos backups para otimizar espaço
- Retenção automática (mantém últimos 5 backups)
- Scripts multiplataforma (Linux/Windows)

### Monitoramento Pós-Deploy
- Verificação automática de performance
- Testes de carga básicos
- Verificação de headers de segurança
- Alertas automáticos em caso de problemas

### Segurança
- Scan de vulnerabilidades com Trivy
- Auditoria de dependências com Composer
- Verificação semanal automática
- Upload de resultados para GitHub Security

### Rollback Automático
- Rollback automático em caso de falha
- Backup disponível para restauração manual
- Logs detalhados para troubleshooting

### Performance
- Thresholds configuráveis por ambiente
- Staging: máximo 5 segundos
- Produção: máximo 3 segundos
- Testes de múltiplos endpoints

## Troubleshooting

### Falha nos Testes
- Verifique os logs no GitHub Actions
- Execute os testes localmente para depuração

### Falha no Deploy
- O pipeline para em caso de falha nos testes
- Deploy de produção inclui rollback automático

### Problemas de Conexão com Banco
- Verifique configurações em `.env.staging` e `.env.production`
- Confirme que o PostgreSQL está acessível
