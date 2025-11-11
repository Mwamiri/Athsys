# 🚀 QUICK START - Athlete Results System Installation

## ⚡ 30 Second Setup

### Step 1: Open in Browser
```
http://your-domain.com/installer.php
```

### Step 2: Follow Wizard (6 Steps)
1. Welcome screen → Click "Start"
2. Enter database credentials → Click "Test Connection"
3. Choose database name → Click "Create Database"
4. Wait for schema import (automatic)
5. Configuration saves automatically
6. Click "Go to Dashboard"

### Step 3: Done! 🎉
Your system is ready to use.

---

## 📁 Installation Files

| File | Purpose | Access |
|------|---------|--------|
| **installer.php** | Main wizard | `http://domain.com/installer.php` |
| **status.php** | Check status | `http://domain.com/status.php` |
| **install/api.php** | Backend API | Auto-used by wizard |
| **install/helper.php** | Utilities | CLI: `php install/helper.php` |

---

## 🎯 What Happens During Installation

```
Input Credentials
    ↓
Test Connection ✓
    ↓
Create Database ✓
    ↓
Import Schema ✓
    ↓
Save Config ✓
    ↓
Installation Lock Created ✓
    ↓
Ready to Use!
```

---

## ❓ Frequently Asked Questions

### How long does it take?
**2-3 minutes** - Most of the time is waiting for schema import.

### What if I mess up?
Visit `installer.php?force=1` to start over.

### Is my data secure?
Yes! Database credentials are encrypted in `config/database.php`.

### Can I delete the installer?
Yes! After installation, delete `installer.php` and `install/api.php` for security.

### How do I check the status?
Visit `status.php` anytime to see installation status.

---

## 🔧 If Something Goes Wrong

### Can't Access installer.php?
- Check URL: `http://your-domain.com/installer.php`
- Verify file exists
- Clear browser cache

### Database Connection Failed?
- Check MySQL is running
- Verify credentials are correct
- Test from command line:
  ```
  mysql -h localhost -u username -p
  ```

### Schema Import Failed?
- Ensure `install/database.sql` exists
- Check database user has privileges
- Try manual import in phpMyAdmin

### Still Stuck?
Read: `INSTALLATION-GUIDE.md` (comprehensive troubleshooting)

---

## 📖 Documentation

- **INSTALLATION-GUIDE.md** - Full guide with troubleshooting
- **INSTALLATION-SYSTEM.md** - Technical documentation
- **INSTALL-WIZARD.md** - Wizard overview

---

## ✅ Installation Checklist

- [ ] Files uploaded to server
- [ ] Visited installer.php
- [ ] Entered database credentials
- [ ] Database created successfully
- [ ] Schema imported
- [ ] Configuration saved
- [ ] Can access dashboard
- [ ] Deleted installer.php (security)
- [ ] Changed admin password

---

## 🎓 After Installation

1. **Delete installer files:**
   ```bash
   rm installer.php install/api.php
   ```

2. **Change admin password**
   - Login to dashboard
   - Go to Settings → Admin Users
   - Change password

3. **Create backups**
   - Backup database
   - Backup config/database.php

---

## 📞 Quick Reference

| What | Where |
|------|-------|
| **Start Installation** | `installer.php` |
| **Check Status** | `status.php` |
| **Full Guide** | `INSTALLATION-GUIDE.md` |
| **Technical Docs** | `INSTALLATION-SYSTEM.md` |
| **Help & Debug** | `install/helper.php` |

---

## 🎯 URLs to Remember

```
Installer:    http://your-domain.com/installer.php
Status:       http://your-domain.com/status.php
Dashboard:    http://your-domain.com/index.php
Docs:         Read INSTALLATION-GUIDE.md
```

---

**Installation System v1.0**  
🟢 Ready to Install  
⏱️ Takes 2-3 minutes  
✅ Production Ready

**Go to: `installer.php` and start now!**
