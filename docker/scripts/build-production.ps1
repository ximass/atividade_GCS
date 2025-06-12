# Script PowerShell para build e deploy do ambiente production
Write-Host "🔨 Fazendo build do ambiente PRODUCTION..." -ForegroundColor Green

# Para o container atual se estiver rodando
docker-compose -f docker/docker-compose.prod.yml down

# Faz o build da nova imagem
docker-compose -f docker/docker-compose.prod.yml build --no-cache

Write-Host "🚀 Iniciando containers do PRODUCTION..." -ForegroundColor Green
docker-compose -f docker/docker-compose.prod.yml up -d

Write-Host "⏳ Aguardando containers inicializarem..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

Write-Host "🔄 Executando deploy..." -ForegroundColor Green
docker exec -it laravel-prod-app /usr/local/bin/deploy.sh

Write-Host "✅ Build e deploy do PRODUCTION concluídos!" -ForegroundColor Green
