# Script PowerShell para diagnóstico completo
Write-Host "🔧 Diagnóstico Completo - Laravel Docker + PostgreSQL Externo" -ForegroundColor Green
Write-Host "================================================================" -ForegroundColor Green
Write-Host ""

$ErrorActionPreference = "Continue"

# Função para executar comandos com tratamento de erro
function Execute-Command {
    param(
        [string]$Command,
        [string]$Description
    )
    
    Write-Host "→ $Description..." -ForegroundColor Yellow
    try {
        $result = Invoke-Expression $Command 2>&1
        if ($LASTEXITCODE -eq 0 -or $result) {
            Write-Host "✅ $Description - OK" -ForegroundColor Green
            return $true
        } else {
            Write-Host "❌ $Description - FALHOU" -ForegroundColor Red
            return $false
        }
    } catch {
        Write-Host "❌ $Description - ERRO: $($_.Exception.Message)" -ForegroundColor Red
        return $false
    }
}

Write-Host "1️⃣ VERIFICAÇÕES BÁSICAS" -ForegroundColor Cyan
Write-Host "========================" -ForegroundColor Cyan

Execute-Command "docker --version" "Verificando Docker"
Execute-Command "docker-compose --version" "Verificando Docker Compose"

Write-Host ""
Write-Host "2️⃣ CONECTIVIDADE DE REDE" -ForegroundColor Cyan
Write-Host "==========================" -ForegroundColor Cyan

$DB_HOST = "177.44.248.74"
$DB_PORT = 5432

Execute-Command "Test-Connection -ComputerName $DB_HOST -Count 1 -Quiet" "Ping para $DB_HOST"

try {
    $tcpClient = New-Object System.Net.Sockets.TcpClient
    $tcpClient.Connect($DB_HOST, $DB_PORT)
    $tcpClient.Close()
    Write-Host "✅ Porta $DB_PORT acessível em $DB_HOST" -ForegroundColor Green
} catch {
    Write-Host "❌ Porta $DB_PORT não acessível em $DB_HOST" -ForegroundColor Red
}

Write-Host ""
Write-Host "3️⃣ ARQUIVOS DE CONFIGURAÇÃO" -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan

$requiredFiles = @(
    ".env.staging",
    ".env.production", 
    "docker\docker-compose.staging.yml",
    "docker\docker-compose.prod.yml",
    "docker\Dockerfile.staging",
    "docker\Dockerfile.production"
)

foreach ($file in $requiredFiles) {
    if (Test-Path $file) {
        Write-Host "✅ $file existe" -ForegroundColor Green
    } else {
        Write-Host "❌ $file não encontrado" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "4️⃣ CONTAINERS DOCKER" -ForegroundColor Cyan
Write-Host "=====================" -ForegroundColor Cyan

$containers = docker ps -a --filter "name=laravel" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"
if ($containers) {
    Write-Host "Containers Laravel:" -ForegroundColor White
    Write-Host $containers -ForegroundColor Gray
} else {
    Write-Host "❌ Nenhum container Laravel encontrado" -ForegroundColor Red
}

Write-Host ""
Write-Host "5️⃣ TESTE DE BUILD" -ForegroundColor Cyan
Write-Host "==================" -ForegroundColor Cyan

Write-Host "→ Testando build do ambiente staging..." -ForegroundColor Yellow
$buildResult = docker-compose -f docker\docker-compose.staging.yml build --no-cache 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Build staging - OK" -ForegroundColor Green
} else {
    Write-Host "❌ Build staging - FALHOU" -ForegroundColor Red
    Write-Host "Erro:" -ForegroundColor Red
    Write-Host $buildResult -ForegroundColor Red
}

Write-Host ""
Write-Host "6️⃣ RECOMENDAÇÕES" -ForegroundColor Cyan
Write-Host "=================" -ForegroundColor Cyan

Write-Host "Para resolver problemas de conectividade:" -ForegroundColor White
Write-Host "1. Execute: .\docker\scripts\test-db-connection.ps1" -ForegroundColor Gray
Write-Host "2. Consulte: .\docker\TROUBLESHOOTING.md" -ForegroundColor Gray
Write-Host "3. Verifique configuração do PostgreSQL no servidor 177.44.248.74" -ForegroundColor Gray

Write-Host ""
Write-Host "🏁 Diagnóstico completo finalizado!" -ForegroundColor Green
