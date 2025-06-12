#!/bin/bash

echo "🔍 Testando conectividade com banco PostgreSQL externo..."

DB_HOST="177.44.248.74"
DB_PORT="5432"
DB_USER="postgres"
DB_PASSWORD="postgres"
DB_NAME_STAGING="staging"
DB_NAME_PRODUCTION="production"

echo "→ Testando conectividade de rede..."
if ping -c 1 $DB_HOST > /dev/null 2>&1; then
    echo "✅ Host $DB_HOST está acessível"
else
    echo "❌ Host $DB_HOST não está acessível via ping"
fi

echo "→ Testando porta PostgreSQL..."
if nc -z $DB_HOST $DB_PORT; then
    echo "✅ Porta $DB_PORT está aberta em $DB_HOST"
else
    echo "❌ Porta $DB_PORT não está acessível em $DB_HOST"
fi

echo "→ Testando conexão PostgreSQL (staging)..."
if psql "postgresql://$DB_USER:$DB_PASSWORD@$DB_HOST:$DB_PORT/$DB_NAME_STAGING" -c "SELECT version();" > /dev/null 2>&1; then
    echo "✅ Conexão com banco staging bem-sucedida"
else
    echo "❌ Falha na conexão com banco staging"
    echo "   Erro detalhado:"
    psql "postgresql://$DB_USER:$DB_PASSWORD@$DB_HOST:$DB_PORT/$DB_NAME_STAGING" -c "SELECT version();" 2>&1 | head -5
fi

echo "→ Testando conexão PostgreSQL (production)..."
if psql "postgresql://$DB_USER:$DB_PASSWORD@$DB_HOST:$DB_PORT/$DB_NAME_PRODUCTION" -c "SELECT version();" > /dev/null 2>&1; then
    echo "✅ Conexão com banco production bem-sucedida"
else
    echo "❌ Falha na conexão com banco production"
    echo "   Erro detalhado:"
    psql "postgresql://$DB_USER:$DB_PASSWORD@$DB_HOST:$DB_PORT/$DB_NAME_PRODUCTION" -c "SELECT version();" 2>&1 | head -5
fi

echo "→ Verificando configuração SSL..."
echo "   Testando conexão sem SSL..."
if psql "postgresql://$DB_USER:$DB_PASSWORD@$DB_HOST:$DB_PORT/$DB_NAME_STAGING?sslmode=disable" -c "SELECT 1;" > /dev/null 2>&1; then
    echo "✅ Conexão sem SSL funciona"
else
    echo "❌ Conexão sem SSL falhou"
fi

echo "🏁 Teste de conectividade concluído!"
