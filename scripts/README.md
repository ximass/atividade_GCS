# Scripts de CI/CD

Esta pasta contém scripts para facilitar o desenvolvimento e teste do pipeline CI/CD.

## Scripts Disponíveis

### 1. `test-pipeline.ps1`
**Descrição**: Testa o pipeline completo localmente antes de fazer push.
**Uso**: 
```powershell
.\scripts\test-pipeline.ps1
```
**O que faz**:
- Copia `.env.staging` para `.env`
- Instala dependências do Composer
- Gera chave da aplicação
- Executa migrations
- Roda todos os testes
- Opcionalmente builda imagem Docker

### 2. `deploy-local.ps1`
**Descrição**: Faz deploy manual local para testar os containers.
**Uso**:
```powershell
# Para staging
.\scripts\deploy-local.ps1 -Environment staging

# Para produção
.\scripts\deploy-local.ps1 -Environment production
```
**O que faz**:
- Para containers existentes
- Builda nova imagem Docker
- Inicia containers com docker-compose
- Executa migrations no container
- Otimiza cache (apenas em produção)
- Verifica saúde da aplicação

### 3. `backup-database.ps1` / `backup-database.sh`
**Descrição**: Scripts de backup do banco de dados (usado pelo CI/CD).
**Uso**:
```powershell
# Windows
.\scripts\backup-database.ps1 -Environment production

# Linux (no CI/CD)
./scripts/backup-database.sh production
```

## URLs dos Ambientes

- **Staging**: http://localhost:8080
- **Production**: http://localhost:80
- **Health Check**: `/health` em qualquer ambiente

## Comandos Úteis

### Logs dos Containers
```powershell
# Ver logs do staging
docker-compose -f docker/docker-compose.staging.yml logs -f

# Ver logs da produção
docker-compose -f docker/docker-compose.prod.yml logs -f
```

### Parar Containers
```powershell
# Parar staging
docker-compose -f docker/docker-compose.staging.yml down

# Parar produção
docker-compose -f docker/docker-compose.prod.yml down
```

### Executar Comandos no Container
```powershell
# No staging
docker exec laravel-staging-app php artisan [comando]

# Na produção
docker exec laravel-prod-app php artisan [comando]
```

## Workflow Recomendado

1. **Desenvolvimento**:
   ```powershell
   # Testa pipeline localmente
   .\scripts\test-pipeline.ps1
   
   # Testa deploy staging local
   .\scripts\deploy-local.ps1 -Environment staging
   ```

2. **Deploy Staging**:
   ```bash
   git push origin staging
   # GitHub Actions fará o deploy automaticamente
   ```

3. **Deploy Produção**:
   ```bash
   git push origin main
   # GitHub Actions fará o deploy automaticamente
   ```
