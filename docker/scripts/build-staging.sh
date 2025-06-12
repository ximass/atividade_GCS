#!/bin/bash

# Script para fazer build e deploy do ambiente staging
echo "🔨 Fazendo build do ambiente STAGING..."

# Para o container atual se estiver rodando
docker-compose -f docker/docker-compose.staging.yml down

# Faz o build da nova imagem
docker-compose -f docker/docker-compose.staging.yml build --no-cache

echo "🚀 Iniciando containers do STAGING..."
docker-compose -f docker/docker-compose.staging.yml up -d

echo "⏳ Aguardando containers inicializarem..."
sleep 10

echo "🔄 Executando deploy..."
docker exec -it laravel-staging-app /usr/local/bin/deploy.sh

echo "✅ Build e deploy do STAGING concluídos!"
