# Script para aplicar correções automáticas comuns
Write-Host "🔧 Aplicando correções automáticas..." -ForegroundColor Green

# Correção 1: Garantir que os arquivos .env tenham as configurações SSL corretas
Write-Host "→ Verificando configurações SSL nos arquivos .env..." -ForegroundColor Yellow

$envFiles = @(".env.staging", ".env.production")

foreach ($envFile in $envFiles) {
    if (Test-Path $envFile) {
        $content = Get-Content $envFile
        
        # Verifica se já tem configuração SSL
        if ($content -notmatch "DB_SSLMODE") {
            Write-Host "  Adicionando configurações SSL ao $envFile" -ForegroundColor Gray
            
            # Encontra a linha do DB_PASSWORD e adiciona após ela
            $newContent = @()
            foreach ($line in $content) {
                $newContent += $line
                if ($line -match "^DB_PASSWORD=") {
                    $newContent += "# Configurações SSL para PostgreSQL externo"
                    $newContent += "DB_SSLMODE=prefer"
                    $newContent += "DB_SSLCERT="
                    $newContent += "DB_SSLKEY="
                    $newContent += "DB_SSLROOTCERT="
                }
            }
            
            Set-Content -Path $envFile -Value $newContent
            Write-Host "✅ $envFile atualizado com configurações SSL" -ForegroundColor Green
        } else {
            Write-Host "✅ $envFile já possui configurações SSL" -ForegroundColor Green
        }
    } else {
        Write-Host "❌ $envFile não encontrado" -ForegroundColor Red
    }
}

# Correção 2: Verificar se os scripts têm permissão de execução (no contexto Windows isso é automático)
Write-Host "→ Verificando scripts de deploy..." -ForegroundColor Yellow

$scripts = @(
    "docker\scripts\deploy-staging.sh",
    "docker\scripts\deploy-production.sh", 
    "docker\scripts\test-container-db.sh"
)

foreach ($script in $scripts) {
    if (Test-Path $script) {
        Write-Host "✅ $script existe" -ForegroundColor Green
    } else {
        Write-Host "❌ $script não encontrado" -ForegroundColor Red
    }
}

# Correção 3: Criar .env.example se não existir
Write-Host "→ Verificando arquivo .env.example..." -ForegroundColor Yellow

if (-not (Test-Path ".env.example")) {
    if (Test-Path ".env.staging") {
        Copy-Item ".env.staging" ".env.example"
        Write-Host "✅ .env.example criado baseado no .env.staging" -ForegroundColor Green
    } else {
        Write-Host "❌ Não foi possível criar .env.example" -ForegroundColor Red
    }
} else {
    Write-Host "✅ .env.example já existe" -ForegroundColor Green
}

# Correção 4: Verificar e corrigir configurações no config/database.php
Write-Host "→ Verificando config/database.php..." -ForegroundColor Yellow

if (Test-Path "config\database.php") {
    $dbConfig = Get-Content "config\database.php" -Raw
    
    if ($dbConfig -match "DB_SSLMODE") {
        Write-Host "✅ config/database.php já configurado para SSL" -ForegroundColor Green
    } else {
        Write-Host "⚠️  config/database.php pode precisar de configuração SSL manual" -ForegroundColor Yellow
        Write-Host "   Verifique se a seção 'pgsql' inclui configurações SSL" -ForegroundColor Gray
    }
} else {
    Write-Host "❌ config/database.php não encontrado" -ForegroundColor Red
}

Write-Host ""
Write-Host "🎯 Próximos passos recomendados:" -ForegroundColor Cyan
Write-Host "1. Execute: .\docker\scripts\diagnose-all.ps1" -ForegroundColor Gray
Write-Host "2. Execute: .\docker\scripts\test-db-connection.ps1" -ForegroundColor Gray  
Write-Host "3. Se houver problemas, consulte: .\docker\TROUBLESHOOTING.md" -ForegroundColor Gray
Write-Host "4. Faça o build: .\docker\scripts\build-staging.ps1" -ForegroundColor Gray

Write-Host ""
Write-Host "✅ Correções automáticas aplicadas!" -ForegroundColor Green
