# Deploy Docker - Ambientes Separados

Este projeto foi configurado para rodar dois ambientes Docker separados:
- **Staging**: Branch `staging` na porta 8080
- **Production**: Branch `master` na porta 80

## 📁 Estrutura dos Arquivos

```
docker/
├── docker-compose.staging.yml    # Configuração do ambiente staging
├── docker-compose.prod.yml       # Configuração do ambiente production
├── Dockerfile.staging            # Dockerfile específico para staging
├── Dockerfile.production         # Dockerfile específico para production
├── nginx/
│   ├── staging.conf              # Configuração nginx staging
│   └── production.conf           # Configuração nginx production
└── scripts/
    ├── build-staging.ps1         # Script PowerShell para deploy staging
    ├── build-production.ps1      # Script PowerShell para deploy production
    ├── build-staging.sh          # Script Bash para deploy staging
    ├── build-production.sh       # Script Bash para deploy production
    ├── deploy-staging.sh         # Script interno do container staging
    └── deploy-production.sh      # Script interno do container production
```

## 🚀 Como Usar

### Opção 1: Scripts Automatizados (Recomendado)

**Para Windows (PowerShell):**
```powershell
# Deploy do ambiente Staging
.\docker\scripts\build-staging.ps1

# Deploy do ambiente Production
.\docker\scripts\build-production.ps1
```

**Para Linux/Mac (Bash):**
```bash
# Deploy do ambiente Staging
./docker/scripts/build-staging.sh

# Deploy do ambiente Production
./docker/scripts/build-production.sh
```

### Opção 2: Comandos Manuais

**Staging:**
```powershell
# Build e start do staging
docker-compose -f docker/docker-compose.staging.yml down
docker-compose -f docker/docker-compose.staging.yml build --no-cache
docker-compose -f docker/docker-compose.staging.yml up -d

# Deploy manual (opcional)
docker exec -it laravel-staging-app /usr/local/bin/deploy.sh
```

**Production:**
```powershell
# Build e start do production
docker-compose -f docker/docker-compose.prod.yml down
docker-compose -f docker/docker-compose.prod.yml build --no-cache
docker-compose -f docker/docker-compose.prod.yml up -d

# Deploy manual (opcional)
docker exec -it laravel-prod-app /usr/local/bin/deploy.sh
```

## 🔧 O que foi corrigido

### Problema Original
- Ambos containers compartilhavam o mesmo volume (`../:/var/www`)
- Git checkout em um container afetava o outro
- Conflitos entre branches diferentes
- **Comando `php artisan test` não estava disponível**

### Solução Implementada
1. **Dockerfiles separados**: `Dockerfile.staging` e `Dockerfile.production`
2. **Volumes independentes**: Cada ambiente tem seus próprios volumes
3. **Redes separadas**: `laravel_staging_net` e `laravel_prod_net`
4. **Scripts de deploy internos**: Cada container tem seu próprio script
5. **Build independente**: Código é copiado durante o build, não montado
6. **Comandos de teste corrigidos**: Scripts agora tentam `php artisan test` primeiro, e usam `./vendor/bin/phpunit` como fallback

### Benefícios
- ✅ Isolamento completo entre ambientes
- ✅ Branches diferentes em cada container
- ✅ Deploys independentes
- ✅ Volumes separados para storage e cache
- ✅ Configurações específicas por ambiente
- ✅ Comandos de teste robustos com fallback automático

## 🧪 Testando os Comandos

Você pode testar se os comandos estão funcionando corretamente:

```bash
# Testar comandos de teste disponíveis
./docker/scripts/test-commands.sh
```

## 📝 Arquivos de Ambiente

Certifique-se de ter os arquivos:
- `.env.staging` - Configurações do ambiente staging
- `.env.production` - Configurações do ambiente production

## 🌐 Acesso

- **Staging**: http://localhost:8080
- **Production**: http://localhost:80

## 🔍 Logs

```powershell
# Ver logs do staging
docker-compose -f docker/docker-compose.staging.yml logs -f

# Ver logs do production
docker-compose -f docker/docker-compose.prod.yml logs -f
```

## 🛑 Parar Ambientes

```powershell
# Parar staging
docker-compose -f docker/docker-compose.staging.yml down

# Parar production
docker-compose -f docker/docker-compose.prod.yml down
```
