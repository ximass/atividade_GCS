#!/bin/bash

# Script para fazer build e deploy do ambiente production
echo "🔨 Fazendo build do ambiente PRODUCTION..."

# Para o container atual se estiver rodando
docker-compose -f docker/docker-compose.prod.yml down

# Faz o build da nova imagem
docker-compose -f docker/docker-compose.prod.yml build --no-cache

echo "🚀 Iniciando containers do PRODUCTION..."
docker-compose -f docker/docker-compose.prod.yml up -d

echo "⏳ Aguardando containers inicializarem..."
sleep 10

echo "🔄 Executando deploy..."
docker exec -it laravel-prod-app /usr/local/bin/deploy.sh

echo "✅ Build e deploy do PRODUCTION concluídos!"
