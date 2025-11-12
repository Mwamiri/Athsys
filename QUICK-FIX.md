# 🚨 Can't Login? Quick Fix Guide

## The Problem
❌ You're trying to login but getting errors or being redirected

## The Solution
✅ Run the setup wizard - it only takes 2 minutes!

---

## 📝 Quick Setup (2 Minutes)

### Step 1: Go to Setup
Visit: `http://yourdomain.com/setup.php`

### Step 2: Enter Database Info
You need these 4 things:
- **Host:** `localhost` (usually)
- **Database Name:** Create in cPanel first (e.g., `athletes`)
- **Username:** Your database username (from cPanel)
- **Password:** Your database password

💡 **Don't have a database?** Create one in cPanel → MySQL Databases first!

### Step 3: Create Admin Account
- Email: Your email
- Password: At least 6 characters
- Name: Your first and last name

### Step 4: Done!
Setup wizard will:
- ✅ Create all tables
- ✅ Save configuration
- ✅ Redirect to login

**Now you can login!**

---

## 🔑 Default Test Credentials

If you ran the setup wizard, use the credentials you created.

If you imported the database manually, try:
- Email: `admin@example.com`
- Password: `password123`

⚠️ **Change this password immediately after login!**

---

## 🆘 Still Not Working?

### Quick Checks
1. Is MySQL running? (WAMP users: check system tray)
2. Did you create the database in cPanel?
3. Are your database credentials correct?
4. Did you complete ALL setup steps?

### Diagnostic Tools
Visit these URLs to check:
- `test-db.php` - Test database connection
- `login-troubleshooting.php` - Check user accounts
- `status.php` - Overall system status

### Clear Cache
Sometimes helps:
1. Press `Ctrl+Shift+Delete`
2. Clear browsing data
3. Try again in incognito/private mode

---

## 📖 Need More Details?

See these guides:
- `SETUP-REQUIRED.md` - Complete setup guide
- `LOGIN-TROUBLESHOOTING.md` - Login issues
- `README.md` - General installation

---

## 💬 Common Questions

**Q: Why do I need to run setup?**  
A: The system needs database configuration to work. Setup creates this for you.

**Q: I already have a database, why doesn't it work?**  
A: The system needs `config/database.php` file with your credentials. Setup creates this.

**Q: Can I skip the wizard?**  
A: Yes! See `SETUP-REQUIRED.md` for manual setup instructions.

**Q: Is this a security issue?**  
A: No! This is normal for first-time installation. The config file contains sensitive data so it's not in the repository.

**Q: Will I lose my data?**  
A: No! If you have existing data, the setup won't delete it.

---

## ✅ Checklist Before Seeking Help

- [ ] MySQL/MariaDB is running
- [ ] Database exists (created in cPanel)
- [ ] Ran setup wizard completely
- [ ] Used correct database credentials
- [ ] Tried clearing browser cache
- [ ] Checked diagnostic tools (test-db.php)
- [ ] Read SETUP-REQUIRED.md

If all checked and still not working, check server error logs or contact support with:
- Error message (exact text)
- Screenshots from diagnostic tools
- Server/PHP version info

---

**Last Updated:** November 12, 2025  
**Quick Ref:** For users who can't login  
**Full Guide:** See SETUP-REQUIRED.md
