# Manual Deploy Script for Local Testing
# This script simulates the CI/CD pipeline deployment process

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("staging", "production")]
    [string]$Environment
)

Write-Host "🚀 Starting manual deployment to $Environment..." -ForegroundColor Green

# Set environment-specific variables
if ($Environment -eq "staging") {
    $ComposeFile = "docker/docker-compose.staging.yml"
    $ContainerName = "laravel-staging-app"
    $ImageTag = "laravel-staging:latest"
    $Port = "8080"
} else {
    $ComposeFile = "docker/docker-compose.prod.yml"
    $ContainerName = "laravel-prod-app"
    $ImageTag = "laravel-production:latest"
    $Port = "80"
}

# Stop existing containers
Write-Host "🛑 Stopping existing containers..." -ForegroundColor Blue
docker-compose -f $ComposeFile down 2>$null

# Build Docker image
Write-Host "🏗️ Building Docker image..." -ForegroundColor Blue
docker build -f docker/Dockerfile -t $ImageTag .
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Docker build failed" -ForegroundColor Red
    exit 1
}

# Start new containers
Write-Host "🐳 Starting new containers..." -ForegroundColor Blue
docker-compose -f $ComposeFile up -d
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Container startup failed" -ForegroundColor Red
    exit 1
}

# Wait for services to be ready
Write-Host "⏳ Waiting for services to be ready..." -ForegroundColor Blue
Start-Sleep -Seconds 30

# Run migrations in container
Write-Host "🗄️ Running migrations in container..." -ForegroundColor Blue
docker exec $ContainerName php artisan migrate --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Migrations failed in container" -ForegroundColor Red
    exit 1
}

# Cache optimization for production
if ($Environment -eq "production") {
    Write-Host "⚡ Optimizing cache for production..." -ForegroundColor Blue
    docker exec $ContainerName php artisan config:cache
    docker exec $ContainerName php artisan route:cache
    docker exec $ContainerName php artisan view:cache
}

# Health check
Write-Host "🏥 Checking application health..." -ForegroundColor Blue
Start-Sleep -Seconds 10

$HealthUrl = "http://localhost:$Port/health"
try {
    $Response = Invoke-WebRequest -Uri $HealthUrl -UseBasicParsing -TimeoutSec 10
    if ($Response.StatusCode -eq 200) {
        Write-Host "✅ Health check passed" -ForegroundColor Green
    } else {
        Write-Host "⚠️ Health check returned status: $($Response.StatusCode)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️ Health check failed, but deployment may still be successful" -ForegroundColor Yellow
    Write-Host "Try accessing: http://localhost:$Port" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "🎉 Deployment to $Environment completed!" -ForegroundColor Green
Write-Host "🔗 Application URL: http://localhost:$Port" -ForegroundColor Cyan
Write-Host "🏥 Health Check: $HealthUrl" -ForegroundColor Cyan

# Show container status
Write-Host ""
Write-Host "📊 Container Status:" -ForegroundColor Blue
docker-compose -f $ComposeFile ps
