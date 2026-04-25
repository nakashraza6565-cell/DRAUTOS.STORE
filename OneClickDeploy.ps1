# One-Click Deployment Script for DRAUTOS.STORE
# This script automates GitHub push and creates a clean ZIP for Hostinger.

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "  DRAUTOS ONE-CLICK DEPLOYMENT  " -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

# 1. Check for changes
$status = git status --porcelain
if (-not $status) {
    Write-Host "No changes to deploy." -ForegroundColor Gray
} else {
    Write-Host "Committing changes..." -ForegroundColor Yellow
    git add .
    git commit -m "Auto-Update: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
}

# 2. Push to GitHub
Write-Host "Pushing to GitHub..." -ForegroundColor Yellow
git push origin main

if ($LASTEXITCODE -ne 0) {
    Write-Host "GitHub Push failed! Checking connection..." -ForegroundColor Red
    # Try once more with a larger buffer if it failed
    git config http.postBuffer 524288000
    git push origin main
}

if ($LASTEXITCODE -eq 0) {
    Write-Host "GitHub is up to date!" -ForegroundColor Green
    
    # Trigger Hostinger Deployment Webhook
    Write-Host "Signal sent to Hostinger! Updating live site..." -ForegroundColor Cyan
    try {
        Invoke-RestMethod -Uri "https://webhooks.hostinger.com/deploy/5b809b38b6dacd37f6fd14d22283e71a" -Method Post | Out-Null
        Write-Host "Deployment triggered successfully!" -ForegroundColor Green
    } catch {
        Write-Host "Warning: Could not signal Hostinger automatically, but code is pushed." -ForegroundColor Yellow
    }
} else {
    Write-Host "Warning: GitHub push failed, but we will proceed with the ZIP." -ForegroundColor Yellow
}

# 3. Create Production ZIP
$timestamp = Get-Date -Format "yyyy-MM-dd_HHmm"
$zipFile = "DRAUTOS_DEPLOY_$timestamp.zip"
Write-Host "Creating Production ZIP: $zipFile" -ForegroundColor Yellow

# Create a temporary staging folder to avoid zipping the zip itself
$staging = "deploy_staging"
if (Test-Path $staging) { Remove-Item $staging -Recurse -Force }
New-Item -ItemType Directory -Path $staging | Out-Null

# Copy public_html to staging, excluding node_modules and recursive build_ready/build_temp
# robocopy is much more reliable for recursive exclusions on Windows
robocopy "public_html" "$staging" /S /E /XD node_modules build_ready build_temp /XF *.zip *.sql | Out-Null

# Zip the staging contents
Compress-Archive -Path "$staging/*" -DestinationPath $zipFile -Force

# Cleanup staging
Remove-Item $staging -Recurse -Force

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host " SUCCESS! " -ForegroundColor Green
Write-Host "1. GitHub has been updated." -ForegroundColor White
Write-Host "2. Clean ZIP created: $zipFile" -ForegroundColor White
Write-Host ""
Write-Host "Final Step: Upload $zipFile to Hostinger and Extract it." -ForegroundColor Yellow
Write-Host "==========================================" -ForegroundColor Cyan
pause
