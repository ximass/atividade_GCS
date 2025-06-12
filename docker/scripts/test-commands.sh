#!/bin/bash

echo "🧪 Testando comandos de teste disponíveis..."

echo "→ Testando 'php artisan test'..."
if php artisan test --help 2>/dev/null; then
    echo "✅ 'php artisan test' está disponível"
else
    echo "❌ 'php artisan test' não está disponível"
fi

echo "→ Testando './vendor/bin/phpunit'..."
if ./vendor/bin/phpunit --version 2>/dev/null; then
    echo "✅ './vendor/bin/phpunit' está disponível"
else
    echo "❌ './vendor/bin/phpunit' não está disponível"
fi

echo "→ Testando 'php artisan list'..."
php artisan list | grep -i test

echo "→ Executando teste completo..."
# Tenta primeiro com artisan test, se falhar usa phpunit diretamente
if php artisan test 2>/dev/null; then
    echo "✅ Testes executados com 'php artisan test'"
elif ./vendor/bin/phpunit; then
    echo "✅ Testes executados com './vendor/bin/phpunit'"
else
    echo "❌ Ambos os comandos falharam"
    exit 1
fi

echo "🎉 Teste de comandos concluído!"
