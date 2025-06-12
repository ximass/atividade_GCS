#!/bin/bash
set -e

echo "🚀 Iniciando deploy do ambiente PRODUCTION..."

echo "→ Salvando estado atual do git..."
git stash --include-untracked

echo "→ Fazendo checkout e pull da branch master..."
git checkout master
git pull origin master || { 
    echo "❌ Git pull falhou"; 
    git stash pop; 
    exit 1; 
}

echo "→ Instalando/atualizando dependências..."
composer install --no-dev --optimize-autoloader

echo "→ Testando conectividade com banco de dados..."
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" > /dev/null 2>&1; then
    echo "✅ Conexão with banco bem-sucedida!"
else
    echo "❌ Falha na conexão com banco de dados!"
    echo "   Detalhes do erro:"
    php artisan tinker --execute="try { DB::connection()->getPdo(); } catch (Exception \$e) { echo \$e->getMessage(); }" 2>&1
    exit 1
fi

echo "→ Executando testes..."
# Tenta primeiro com artisan test, se falhar usa phpunit diretamente
if php artisan test 2>/dev/null || ./vendor/bin/phpunit; then
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
    
    echo "✅ Deploy do PRODUCTION concluído com sucesso!"
else
    echo "❌ Testes falharam, revertendo mudanças..."
    git reset --hard HEAD@{1}
    exit 1
fi
