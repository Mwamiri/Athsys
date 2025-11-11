# AthSys GitHub Upload Script
# Run this in PowerShell from c:\wamp64\www\athsys

Write-Host "🚀 AthSys GitHub Upload Helper" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Check if Git is installed
Write-Host "Checking Git installation..." -ForegroundColor Yellow
$gitInstalled = Get-Command git -ErrorAction SilentlyContinue

if (-not $gitInstalled) {
    Write-Host "❌ Git is not installed!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please install Git first:" -ForegroundColor Yellow
    Write-Host "1. Visit: https://git-scm.com/download/win" -ForegroundColor White
    Write-Host "2. Download and install" -ForegroundColor White
    Write-Host "3. Restart PowerShell" -ForegroundColor White
    Write-Host "4. Run this script again" -ForegroundColor White
    Write-Host ""
    Read-Host "Press Enter to exit"
    exit
}

Write-Host "✅ Git is installed!" -ForegroundColor Green
Write-Host ""

# Get GitHub username
Write-Host "Enter your GitHub username:" -ForegroundColor Yellow
$username = Read-Host "Username"

if (-not $username) {
    Write-Host "❌ Username is required!" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit
}

Write-Host ""
Write-Host "📝 Summary:" -ForegroundColor Cyan
Write-Host "Repository will be created at: https://github.com/$username/AthSys" -ForegroundColor White
Write-Host ""

# Confirm
$confirm = Read-Host "Continue? (y/n)"
if ($confirm -ne 'y' -and $confirm -ne 'Y') {
    Write-Host "Cancelled." -ForegroundColor Yellow
    exit
}

Write-Host ""
Write-Host "🔄 Initializing Git repository..." -ForegroundColor Yellow

# Initialize git
git init

Write-Host "✅ Git initialized" -ForegroundColor Green
Write-Host ""

# Add files
Write-Host "📦 Adding files to Git..." -ForegroundColor Yellow
git add .

Write-Host "✅ Files added" -ForegroundColor Green
Write-Host ""

# Commit
Write-Host "💾 Creating initial commit..." -ForegroundColor Yellow
git commit -m "Initial commit: AthSys - Athlete Results Management System"

Write-Host "✅ Commit created" -ForegroundColor Green
Write-Host ""

# Add remote
Write-Host "🔗 Adding GitHub remote..." -ForegroundColor Yellow
git remote add origin "https://github.com/$username/AthSys.git"

Write-Host "✅ Remote added" -ForegroundColor Green
Write-Host ""

# Rename branch to main
Write-Host "🌿 Setting up main branch..." -ForegroundColor Yellow
git branch -M main

Write-Host "✅ Branch configured" -ForegroundColor Green
Write-Host ""

Write-Host "========================" -ForegroundColor Cyan
Write-Host "✅ SETUP COMPLETE!" -ForegroundColor Green
Write-Host "========================" -ForegroundColor Cyan
Write-Host ""

Write-Host "📋 NEXT STEPS:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Go to GitHub.com and login" -ForegroundColor White
Write-Host "2. Create a NEW repository named: AthSys" -ForegroundColor White
Write-Host "3. Make it PUBLIC" -ForegroundColor White
Write-Host "4. DO NOT initialize with README, .gitignore, or license" -ForegroundColor White
Write-Host "5. After creating the repo, come back here" -ForegroundColor White
Write-Host ""

$ready = Read-Host "Repository created on GitHub? (y/n)"

if ($ready -eq 'y' -or $ready -eq 'Y') {
    Write-Host ""
    Write-Host "🚀 Pushing to GitHub..." -ForegroundColor Yellow
    
    git push -u origin main
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "========================" -ForegroundColor Green
        Write-Host "🎉 SUCCESS!" -ForegroundColor Green
        Write-Host "========================" -ForegroundColor Green
        Write-Host ""
        Write-Host "Your project is now on GitHub!" -ForegroundColor Green
        Write-Host "Visit: https://github.com/$username/AthSys" -ForegroundColor Cyan
        Write-Host ""
    } else {
        Write-Host ""
        Write-Host "❌ Push failed!" -ForegroundColor Red
        Write-Host ""
        Write-Host "Common issues:" -ForegroundColor Yellow
        Write-Host "- Repository doesn't exist on GitHub yet" -ForegroundColor White
        Write-Host "- Repository name is different" -ForegroundColor White
        Write-Host "- Authentication failed (check credentials)" -ForegroundColor White
        Write-Host ""
        Write-Host "Try pushing manually:" -ForegroundColor Yellow
        Write-Host "git push -u origin main" -ForegroundColor White
        Write-Host ""
    }
} else {
    Write-Host ""
    Write-Host "When you've created the repository, run:" -ForegroundColor Yellow
    Write-Host "git push -u origin main" -ForegroundColor Cyan
    Write-Host ""
}

Write-Host "Press Enter to exit..."
Read-Host
