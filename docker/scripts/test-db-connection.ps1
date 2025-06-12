# Script PowerShell para testar conectividade com banco PostgreSQL externo
Write-Host "🔍 Testando conectividade com banco PostgreSQL externo..." -ForegroundColor Green

$DB_HOST = "177.44.248.74"
$DB_PORT = 5432
$DB_USER = "postgres"
$DB_PASSWORD = "postgres"
$DB_NAME_STAGING = "staging"
$DB_NAME_PRODUCTION = "production"

Write-Host "→ Testando conectividade de rede..." -ForegroundColor Yellow
if (Test-Connection -ComputerName $DB_HOST -Count 1 -Quiet) {
    Write-Host "✅ Host $DB_HOST está acessível" -ForegroundColor Green
} else {
    Write-Host "❌ Host $DB_HOST não está acessível via ping" -ForegroundColor Red
}

Write-Host "→ Testando porta PostgreSQL..." -ForegroundColor Yellow
try {
    $tcpClient = New-Object System.Net.Sockets.TcpClient
    $tcpClient.Connect($DB_HOST, $DB_PORT)
    $tcpClient.Close()
    Write-Host "✅ Porta $DB_PORT está aberta em $DB_HOST" -ForegroundColor Green
} catch {
    Write-Host "❌ Porta $DB_PORT não está acessível em $DB_HOST" -ForegroundColor Red
    Write-Host "   Erro: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "→ Verificando se o Docker consegue acessar o host..." -ForegroundColor Yellow
try {
    $result = docker run --rm postgres:13 bash -c "timeout 5 bash -c '</dev/tcp/$DB_HOST/$DB_PORT' && echo 'OK' || echo 'FAIL'"
    if ($result -eq "OK") {
        Write-Host "✅ Docker consegue acessar $DB_HOST:$DB_PORT" -ForegroundColor Green
    } else {
        Write-Host "❌ Docker não consegue acessar $DB_HOST:$DB_PORT" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Erro ao testar conectividade via Docker" -ForegroundColor Red
    Write-Host "   Erro: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "→ Testando containers em execução..." -ForegroundColor Yellow
$containers = docker ps --filter "name=laravel" --format "table {{.Names}}\t{{.Status}}"
if ($containers) {
    Write-Host "Containers Laravel em execução:" -ForegroundColor Cyan
    Write-Host $containers -ForegroundColor White
} else {
    Write-Host "❌ Nenhum container Laravel em execução" -ForegroundColor Red
}

Write-Host "🏁 Teste de conectividade concluído!" -ForegroundColor Green
Write-Host ""
Write-Host "💡 Próximos passos recomendados:" -ForegroundColor Cyan
Write-Host "1. Se a conectividade falhou, verifique:" -ForegroundColor White
Write-Host "   - Firewall do servidor PostgreSQL" -ForegroundColor White  
Write-Host "   - Configuração pg_hba.conf para permitir conexões externas" -ForegroundColor White
Write-Host "   - postgresql.conf (listen_addresses)" -ForegroundColor White
Write-Host "2. Para testar dentro do container:" -ForegroundColor White
Write-Host "   docker exec -it laravel-staging-app /usr/local/bin/test-container-db.sh" -ForegroundColor Gray
