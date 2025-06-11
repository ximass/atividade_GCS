# Test CI/CD Pipeline Locally
# Execute this script to test your Laravel application before pushing

Write-Host "🚀 Testing Laravel Application Pipeline..." -ForegroundColor Green

# Check if .env.staging exists
if (-not (Test-Path ".env.staging")) {
    Write-Host "❌ .env.staging file not found!" -ForegroundColor Red
    Write-Host "Please create .env.staging file first" -ForegroundColor Yellow
    exit 1
}

# Copy environment file
Write-Host "📋 Copying environment file..." -ForegroundColor Blue
Copy-Item ".env.staging" ".env" -Force

# Install dependencies
Write-Host "📦 Installing Composer dependencies..." -ForegroundColor Blue
composer install --no-progress --prefer-dist --optimize-autoloader

# Generate application key
Write-Host "🔑 Generating application key..." -ForegroundColor Blue
php artisan key:generate

# Clear config cache
Write-Host "🧹 Clearing config cache..." -ForegroundColor Blue
php artisan config:clear

# Run migrations
Write-Host "🗄️ Running database migrations..." -ForegroundColor Blue
try {
    php artisan migrate --force
    Write-Host "✅ Migrations completed successfully" -ForegroundColor Green
} catch {
    Write-Host "❌ Migrations failed: $_" -ForegroundColor Red
    exit 1
}

# Run tests
Write-Host "🧪 Running tests..." -ForegroundColor Blue
try {
    php artisan test
    Write-Host "✅ All tests passed!" -ForegroundColor Green
} catch {
    Write-Host "❌ Tests failed: $_" -ForegroundColor Red
    exit 1
}

# Build Docker image (optional)
$buildDocker = Read-Host "🐳 Build Docker image? (y/n)"
if ($buildDocker -eq "y" -or $buildDocker -eq "Y") {
    Write-Host "🏗️ Building Docker image..." -ForegroundColor Blue
    docker build -f docker/Dockerfile -t laravel-test:latest .
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Docker image built successfully" -ForegroundColor Green
    } else {
        Write-Host "❌ Docker build failed" -ForegroundColor Red
        exit 1
    }
}

Write-Host ""
Write-Host "🎉 Pipeline test completed successfully!" -ForegroundColor Green
Write-Host "Your application is ready for deployment!" -ForegroundColor Cyan
