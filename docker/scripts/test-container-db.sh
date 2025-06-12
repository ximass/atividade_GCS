#!/bin/bash

echo "🐳 Testando conectividade do container com banco PostgreSQL externo..."

echo "→ Informações do ambiente:"
echo "   Container: $(hostname)"
echo "   Usuario: $(whoami)"
echo "   Data: $(date)"

echo "→ Verificando variáveis de ambiente do banco:"
echo "   DB_HOST: ${DB_HOST:-'não definido'}"
echo "   DB_PORT: ${DB_PORT:-'não definido'}"
echo "   DB_DATABASE: ${DB_DATABASE:-'não definido'}"
echo "   DB_USERNAME: ${DB_USERNAME:-'não definido'}"
echo "   DB_SSLMODE: ${DB_SSLMODE:-'não definido'}"

echo "→ Testando resolução DNS..."
if nslookup $DB_HOST > /dev/null 2>&1; then
    echo "✅ DNS resolvido para $DB_HOST"
    nslookup $DB_HOST
else
    echo "❌ Falha na resolução DNS para $DB_HOST"
fi

echo "→ Testando conectividade TCP..."
if timeout 10 bash -c "</dev/tcp/$DB_HOST/$DB_PORT"; then
    echo "✅ Porta $DB_PORT acessível em $DB_HOST"
else
    echo "❌ Porta $DB_PORT não acessível em $DB_HOST"
fi

echo "→ Testando conexão Laravel com o banco..."
if php artisan tinker --execute="DB::connection()->getPdo(); echo 'Conexão bem-sucedida';" 2>/dev/null; then
    echo "✅ Laravel conseguiu conectar ao banco"
else
    echo "❌ Laravel falhou ao conectar ao banco"
    echo "   Erro detalhado:"
    php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'OK'; } catch (Exception \$e) { echo 'ERRO: ' . \$e->getMessage(); }" 2>&1
fi

echo "→ Testando comando migrate --dry-run..."
if php artisan migrate --dry-run > /dev/null 2>&1; then
    echo "✅ Migrate dry-run bem-sucedido"
else
    echo "❌ Migrate dry-run falhou"
    echo "   Executando migrate com detalhes..."
    php artisan migrate --dry-run 2>&1 | head -10
fi

echo "🏁 Teste de conectividade do container concluído!"
