git add .
git commit -m "Auto-Update: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"
git push origin main
Invoke-RestMethod -Uri "https://webhooks.hostinger.com/deploy/5b809b38b6dacd37f6fd14d22283e71a" -Method Post
Write-Host "Site Updated Successfully!" -ForegroundColor Green
