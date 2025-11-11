# 🚀 GitHub Upload Guide for AthSys

## Quick Setup Instructions

Follow these steps to upload your AthSys project to GitHub.

---

## Step 1: Install Git (If Not Installed)

### Download Git for Windows
1. Visit: https://git-scm.com/download/win
2. Download the installer
3. Run installer with default settings
4. Restart your terminal/PowerShell

---

## Step 2: Create GitHub Repository

### On GitHub.com:

1. **Login** to GitHub.com
2. Click **"+"** (top right) → **"New repository"**
3. **Repository name:** `AthSys`
4. **Description:** `Athlete Results Management System - PHP/MySQL`
5. **Visibility:** ✅ Public
6. **DO NOT** check:
   - Initialize with README (we have one)
   - Add .gitignore (we created one)
   - Choose a license (optional)
7. Click **"Create repository"**

---

## Step 3: Prepare Your Local Files

### Important: Protect Sensitive Data

Before uploading, make sure these files are in `.gitignore`:

```
config/database.php    ← YOUR credentials (DO NOT UPLOAD)
install/.installed     ← Installation lock file
```

We already created `.gitignore` for you!

### Create Example Config

We've created `config/database.example.php` - this is what users will copy and customize.

---

## Step 4: Upload to GitHub

### Method A: Using Git Command Line

Open PowerShell in `c:\wamp64\www\athsys`:

```powershell
# Initialize Git repository
git init

# Add all files (respects .gitignore)
git add .

# First commit
git commit -m "Initial commit: AthSys - Athlete Results Management System"

# Add GitHub remote (replace YOUR_USERNAME with your GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/AthSys.git

# Push to GitHub
git branch -M main
git push -u origin main
```

### Method B: Using GitHub Desktop (Easier)

1. **Download GitHub Desktop:** https://desktop.github.com/
2. **Install and login**
3. **Add repository:**
   - File → Add Local Repository
   - Choose: `c:\wamp64\www\athsys`
   - Click "Add Repository"
4. **Commit:**
   - Write commit message: "Initial commit"
   - Click "Commit to main"
5. **Publish:**
   - Click "Publish repository"
   - Name: `AthSys`
   - Description: "Athlete Results Management System"
   - ✅ Keep public
   - Click "Publish Repository"

---

## Step 5: Verify Upload

### Check on GitHub:

Visit: `https://github.com/YOUR_USERNAME/AthSys`

Should see:
- ✅ All PHP files
- ✅ All documentation (QUICK-START.md, etc.)
- ✅ README.md (main page)
- ✅ install/ folder with database.sql
- ✅ assets/ folder
- ✅ Menilo theme files
- ❌ NO config/database.php (protected)

---

## Step 6: Add Repository Details

### On GitHub repository page:

1. Click **⚙️ Settings** (top right)
2. Scroll to **"About"** section (right sidebar)
3. Click **⚙️ gear icon**
4. Add:
   - **Description:** `Professional athlete results management system with interactive installer and premium Menilo theme`
   - **Website:** (your demo URL if you have one)
   - **Topics:** Add tags:
     - `php`
     - `mysql`
     - `athletics`
     - `sports-management`
     - `athlete-tracking`
     - `competition-management`
     - `menilo-theme`
     - `cakephp`

---

## Step 7: Create a Good README (Already Done!)

Your README.md includes:
- ✅ Project description
- ✅ Features list
- ✅ Installation instructions
- ✅ Screenshots (you can add these later)
- ✅ Demo credentials
- ✅ Documentation links
- ✅ Support information

---

## Step 8: Protect Sensitive Files

### Double-Check .gitignore

Make sure `.gitignore` includes:

```
# Sensitive files
config/database.php
install/.installed
*.log
.env

# Example config is OK
!config/database.example.php
```

### Add Security Notice to README

Already included in your README:

```markdown
## 🔒 Security

⚠️ **Important:** 
- Never commit `config/database.php` with real credentials
- Copy `config/database.example.php` to `config/database.php`
- Update with your database credentials
```

---

## Step 9: Optional Enhancements

### Add Screenshots

Create a `screenshots/` folder with images:
- `installer.png` - Installation wizard
- `dashboard.png` - Main dashboard
- `menilo-theme.png` - Menilo theme

Update README with:
```markdown
## 📸 Screenshots

![Installer](screenshots/installer.png)
![Dashboard](screenshots/dashboard.png)
![Menilo Theme](screenshots/menilo-theme.png)
```

### Create a LICENSE

If you want to add an MIT License:
1. GitHub repository → Add file → Create new file
2. Name: `LICENSE`
3. Click "Choose a license template"
4. Select: MIT License
5. Fill in your name
6. Commit

### Create Contributing Guidelines

File: `CONTRIBUTING.md`
- How to report bugs
- How to suggest features
- Code style guidelines

---

## Step 10: Share Your Project

### Get the Clone URL

On your GitHub repository page:
```
https://github.com/YOUR_USERNAME/AthSys
```

### Share:
- 📧 Email to colleagues
- 💬 Social media
- 📝 Add to your portfolio
- 🌐 Link from your website

### Installation for Others

Users can install with:

```bash
git clone https://github.com/YOUR_USERNAME/AthSys.git
cd AthSys
cp config/database.example.php config/database.php
# Edit database.php with credentials
# Visit installer.php in browser
```

---

## 📋 Upload Checklist

Before pushing to GitHub:

- [ ] Git is installed
- [ ] `.gitignore` file created
- [ ] `config/database.example.php` created (template)
- [ ] `config/database.php` is in .gitignore (NOT uploaded)
- [ ] README.md is complete
- [ ] All documentation files included
- [ ] Repository created on GitHub
- [ ] Files committed and pushed
- [ ] Repository is public
- [ ] Description and topics added
- [ ] README looks good on GitHub

---

## 🆘 Troubleshooting

### "Git not recognized"
- Install Git from https://git-scm.com/download/win
- Restart PowerShell after installation

### "Permission denied"
- Use HTTPS URL (not SSH)
- Or set up SSH keys: https://docs.github.com/en/authentication

### "Repository already exists"
- Choose different name
- Or delete existing repository first

### "Files too large"
- GitHub has 100MB file limit
- Check for large files: `git ls-files --others --exclude-standard -z | xargs -0 du -h | sort -h`

### "Sensitive data uploaded"
- Remove from history: https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/removing-sensitive-data-from-a-repository
- Change all passwords immediately

---

## 🎯 Post-Upload Tasks

1. **Add GitHub badge to README:**
   ```markdown
   ![GitHub stars](https://img.shields.io/github/stars/YOUR_USERNAME/AthSys)
   ![GitHub forks](https://img.shields.io/github/forks/YOUR_USERNAME/AthSys)
   ```

2. **Enable GitHub Pages** (if you want a website):
   - Settings → Pages → Source: main branch

3. **Set up Issues:**
   - Enable issue tracker for bug reports
   - Create issue templates

4. **Create Releases:**
   - Tag version 1.0.0
   - Add release notes
   - Attach .zip file

---

## 📞 Need Help?

- **GitHub Docs:** https://docs.github.com
- **Git Tutorial:** https://git-scm.com/docs/gittutorial
- **GitHub Desktop:** https://desktop.github.com

---

**Ready to share your project with the world!** 🚀

Your AthSys repository will be public at:
`https://github.com/YOUR_USERNAME/AthSys`
