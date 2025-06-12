#!/bin/bash
set -e

echo "🚀 Iniciando deploy do ambiente STAGING..."

echo "→ Salvando estado atual do git..."
git stash --include-untracked

echo "→ Fazendo checkout e pull da branch staging..."
git checkout staging
git pull origin staging || { 
    echo "❌ Git pull falhou"; 
    git stash pop; 
    exit 1; 
}

echo "→ Instalando/atualizando dependências..."
composer install --no-dev --optimize-autoloader

echo "→ Executando testes..."
if php artisan test; then
    echo "✅ Testes passaram!"
    
    echo "→ Rodando migrations..."
    php artisan migrate --force || { 
        echo "❌ Migrations falharam, revertendo..."; 
        git reset --hard HEAD@{1}; 
        exit 1; 
    }
    
    echo "→ Limpando cache..."
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    
    echo "→ Otimizando aplicação..."
    php artisan config:cache
    php artisan view:cache
    
    echo "✅ Deploy do STAGING concluído com sucesso!"
else
    echo "❌ Testes falharam, revertendo mudanças..."
    git reset --hard HEAD@{1}
    exit 1
fi
