# Script PowerShell para build e deploy do ambiente staging
Write-Host "🔨 Fazendo build do ambiente STAGING..." -ForegroundColor Green

# Para o container atual se estiver rodando
docker-compose -f docker/docker-compose.staging.yml down

# Faz o build da nova imagem
docker-compose -f docker/docker-compose.staging.yml build --no-cache

Write-Host "🚀 Iniciando containers do STAGING..." -ForegroundColor Green
docker-compose -f docker/docker-compose.staging.yml up -d

Write-Host "⏳ Aguardando containers inicializarem..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

Write-Host "🔄 Executando deploy..." -ForegroundColor Green
docker exec -it laravel-staging-app /usr/local/bin/deploy.sh

Write-Host "✅ Build e deploy do STAGING concluídos!" -ForegroundColor Green
