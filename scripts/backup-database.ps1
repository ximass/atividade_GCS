# Database Backup Script for CI/CD (PowerShell version)
# This script creates a backup of the database before deployment

param(
    [Parameter(Mandatory=$false)]
    [string]$Environment = "staging"
)

$ErrorActionPreference = "Stop"

# Configuration
$BackupDir = "$env:TEMP\backups"
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"

# Create backup directory
if (-not (Test-Path $BackupDir)) {
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
}

Write-Host "Starting database backup for environment: $Environment"

if ($Environment -eq "production") {
    $DbHost = if ($env:PROD_DB_HOST) { $env:PROD_DB_HOST } else { "177.44.248.74" }
    $DbPort = if ($env:PROD_DB_PORT) { $env:PROD_DB_PORT } else { "5432" }
    $DbName = if ($env:PROD_DB_NAME) { $env:PROD_DB_NAME } else { "production" }
    $DbUser = if ($env:PROD_DB_USER) { $env:PROD_DB_USER } else { "postgres" }
    $BackupFile = "$BackupDir\prod_backup_$Timestamp.sql"
} elseif ($Environment -eq "staging") {
    $DbHost = if ($env:STAGING_DB_HOST) { $env:STAGING_DB_HOST } else { "177.44.248.74" }
    $DbPort = if ($env:STAGING_DB_PORT) { $env:STAGING_DB_PORT } else { "5432" }
    $DbName = if ($env:STAGING_DB_NAME) { $env:STAGING_DB_NAME } else { "staging" }
    $DbUser = if ($env:STAGING_DB_USER) { $env:STAGING_DB_USER } else { "postgres" }
    $BackupFile = "$BackupDir\staging_backup_$Timestamp.sql"
} else {
    Write-Error "Invalid environment: $Environment"
    exit 1
}

# Set PostgreSQL password environment variable
$env:PGPASSWORD = $env:DB_PASSWORD

# Create database backup
Write-Host "Creating backup: $BackupFile"
$pgDumpArgs = @(
    "-h", $DbHost
    "-p", $DbPort
    "-U", $DbUser
    "-d", $DbName
    "--verbose"
    "--clean"
    "--no-owner"
    "--no-privileges"
    "-f", $BackupFile
)

& pg_dump @pgDumpArgs

if ($LASTEXITCODE -ne 0) {
    Write-Error "pg_dump failed with exit code $LASTEXITCODE"
}

# Compress backup
Write-Host "Compressing backup..."
Compress-Archive -Path $BackupFile -DestinationPath "$BackupFile.zip" -Force
Remove-Item $BackupFile
$CompressedBackup = "$BackupFile.zip"

Write-Host "Backup completed: $CompressedBackup"
$BackupSize = (Get-Item $CompressedBackup).Length
Write-Host "Backup size: $([math]::Round($BackupSize / 1MB, 2)) MB"

# Keep only last 5 backups
Write-Host "Cleaning old backups..."
Get-ChildItem -Path $BackupDir -Filter "${Environment}_backup_*.sql.zip" | 
    Sort-Object LastWriteTime -Descending | 
    Select-Object -Skip 5 | 
    Remove-Item -Force

Write-Host "Backup process completed successfully!"
Write-Host "BACKUP_FILE=$CompressedBackup"

# For GitHub Actions output
if ($env:GITHUB_OUTPUT) {
    Add-Content -Path $env:GITHUB_OUTPUT -Value "BACKUP_FILE=$CompressedBackup"
}
