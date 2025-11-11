# 🎯 Athlete Results System - Complete Installation Guide

## 📋 Quick Start

Your new installation system includes:

- **Interactive Installer** (`installer.php`) - Step-by-step guided setup
- **Status Monitor** (`status.php`) - Check installation status anytime
- **API Endpoints** (`install/api.php`) - Backend for installation process

---

## 🚀 Installation Methods

### Option 1: Interactive Installer (Recommended for Everyone)

**Access:** Open `http://your-domain.com/installer.php` in your browser

### Step-by-Step Process

1. **Welcome Screen**
   - Review installation overview
   - Click "Start Installation"

2. **Database Credentials**
   - Enter MySQL Host (usually `localhost` for shared hosting)
   - Enter Database Username
   - Enter Database Password
   - Click "Test Connection"
   - System verifies connection automatically

3. **Database Setup**
   - Enter desired database name (e.g., `athletes` or `athlete_results`)
   - Check "Reset existing database" if you want to overwrite existing data
   - Click "Create Database"
   - Progress bar shows creation status

4. **Schema Import**
   - System automatically imports database tables
   - Progress updates in real-time
   - All tables and initial data are loaded

5. **Save Configuration**
   - System saves your database credentials to `config/database.php`
   - Creates installation lock to prevent re-installation
   - Displays completion status

6. **Success**
   - Click "Go to Dashboard" to access your system
   - Installation is complete!

### Features

- ✅ Beautiful, responsive UI (works on mobile/tablet/desktop)
- ✅ Real-time progress tracking
- ✅ Connection validation before proceeding
- ✅ Database reset option for existing installations
- ✅ Automatic schema import
- ✅ Error messages with solutions
- ✅ Installation lock prevents accidental re-installation

---

### Option 2: Manual Installation

For advanced users or troubleshooting:

### Step 1: Create Database in phpMyAdmin

1. Log into your hosting panel (cPanel/Plesk)
2. Open phpMyAdmin
3. Click "New"
4. Create database with name: `athletes`
5. Choose charset: `utf8mb4_unicode_ci`
6. Create database user with strong password
7. Grant ALL privileges to user

### Step 2: Configure Connection

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'athletes');           // Your database name
define('DB_USER', 'athlete_user');       // Your database user
define('DB_PASS', 'your_password_here'); // Your password
```

### Step 3: Import Schema

In phpMyAdmin:

1. Select your database from left sidebar
2. Click "Import" tab
3. Select `install/database.sql`
4. Click "Go" button
5. Wait for import to complete

### Step 4: Verify Installation

Open `status.php` to confirm all checks pass.

---

## 🔍 Verification & Status

Access `http://your-domain.com/status.php` anytime to check:

- Installation status (Complete/Incomplete)
- Database connection status
- Configuration file presence
- PHP version compatibility
- System permissions

---

## 🛠️ Troubleshooting

### Problem: "Can't access installer.php"

**Solutions:**

- Verify file exists: `c:/wamp64/www/athsys/installer.php`
- Check file permissions (should be 644)
- Try: `http://your-domain/installer.php`
- Clear browser cache and try again

### Problem: "Database connection failed"

**Check:**

1. MySQL server is running
2. Host is correct (usually `localhost`)
3. Username and password are exact
4. Special characters in password are typed correctly

**Test connection manually:**

```bash
mysql -h localhost -u username -p
# Type password when prompted
```

### Problem: "Database creation failed"

**Check:**

1. User has database creation privileges

   ```sql
   GRANT ALL ON *.* TO 'user'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. Sufficient disk space available
3. Database name doesn't already exist (unless resetting)

### Problem: "Schema import failed"

**Check:**

1. File exists: `install/database.sql` (verify file is ~50KB+)
2. Database character set is `utf8mb4`
3. Database user has required privileges
4. File isn't corrupted (open in text editor)

### Problem: "Cannot write configuration file"

**Solutions:**

1. Check `config/` directory permissions:

   ```bash
   chmod 755 config/
   ```

2. PHP must have write access
3. Ensure you're not in read-only mode

### Problem: "Installation already complete"

To re-run installation:

- URL: `http://your-domain/installer.php?force=1`
- Or delete: `install/.installed`

---

## 📊 Installation Features Explained

### Progress Tracking

- 0-16%: Welcome & setup
- 16-33%: Database credentials validation
- 33-50%: Database creation
- 50-80%: Schema import
- 80-100%: Configuration & finalization

### Database Reset

When checked, the installer will:

1. Drop existing database (if present)
2. Create fresh database
3. Import clean schema
4. **WARNING: This deletes all existing data!**

### Installation Lock

After successful installation:

- Lock file created: `install/.installed`
- Contains: Installation date, database name, PHP version
- Prevents accidental re-installation
- Can be deleted to force reinstallation

---

## 🔒 Post-Installation Security

### 1. Delete or Rename Installer

After installation, remove the installer files:

```bash
# Option 1: Delete
rm installer.php install/api.php

# Option 2: Rename (keep as backup)
mv installer.php installer.php.bak
mv install/api.php install/api.php.bak
```

### 2. Secure Configuration File

Ensure `config/database.php` is not accessible:

```bash
chmod 600 config/database.php
```

### 3. Change Default Admin Password

1. Log in with default credentials
2. Go to Settings → Admin Users
3. Change password immediately
4. Disable or delete default account

### 4. Set Proper Permissions

```bash
# Make everything readable
chmod -R 755 /

# Make files non-executable (safer)
find . -type f -exec chmod 644 {} \;

# Keep scripts executable
find . -name "*.php" -exec chmod 644 {} \;

# Writable directories
chmod 755 logs/
chmod 755 tmp/
chmod 755 cache/
```text
```

---

## 📁 File Structure After Installation

```text
project/
├── installer.php              ← Remove after installation
├── status.php                 ← Check installation status
├── config/
│   ├── database.php           ← Auto-generated by installer
│   └── ...
├── install/
│   ├── database.sql           ← SQL schema file
│   ├── api.php                ← Installation API
│   └── .installed             ← Installation lock file
├── assets/
│   └── menilo/                ← Menilo theme files
├── index.php                  ← Main application
└── ...
```

---

## ⚙️ What Gets Installed

The database schema includes:

### Tables Created

- `users` - System users and admins
- `athletes` - Athlete profiles and records
- `results` - Competition results
- `events` - Events and competitions
- `scores` - Athlete scores and rankings
- `system_settings` - Configuration options

### Initial Data

- Admin user account
- Sample athletes
- Sample events
- System configuration

---

## 🚨 Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| **Blank page** | Check PHP error logs |
| **500 error** | Verify config/database.php exists and is valid |
| **Slow installation** | Large database - may take 1-2 minutes |
| **Connection timeout** | Increase PHP timeout or use manual import |
| **Character encoding issues** | Ensure database uses utf8mb4 |
| **Installer stuck on step** | Check browser console for JS errors |

---

## 🎓 For Developers

### Installer API Endpoints

All requests go to: `install/api.php`

### Check Database Connection

```php
POST install/api.php
action=check_database
db_host=localhost
db_user=username
db_pass=password
```

### Create Database

```php
POST install/api.php
action=create_database
db_name=athletes
reset_db=1  // optional
```

### Import Schema

```php
POST install/api.php
action=import_schema
```

### Save Configuration

```php
POST install/api.php
action=save_config
```

## Response Format

All responses are JSON:

```json
{
  "success": true,
  "message": "Operation completed",
  "progress": 75
}
```

---

## 📞 Support & Help

### Getting Help

1. Check this guide first
2. Review error message carefully
3. Check `status.php` for diagnostics
4. Review server error logs
Try manual installation method

### Debug Mode

Check PHP error logs for detailed information:

- **cPanel**: Home → Error Log
- **Plesk**: Tools → Log Files
- **Direct Access**: `/var/log/php-errors.log`

---

## ✅ Installation Checklist

- [ ] Downloaded all files
- [ ] Uploaded files to hosting
- [ ] Database created
- [ ] User created with privileges
- [ ] Ran installer.php
- [ ] All steps completed successfully
- [ ] Tested dashboard access
- [ ] Deleted installer files (security)
- [ ] Changed admin password
- [ ] Created backups

---

## 📝 Next Steps After Installation

1. **Access Dashboard**: Go to `http://your-domain/index.php`
2. **Login**: Use default credentials (shown after installation)
3. **Configure System**: Update settings in Admin Panel
4. **Add Data**: Start entering athletes and events
5. **Customize**: Modify templates and styling

---

**Installation Guide Version:** 1.0  
**Last Updated:** November 11, 2025  
**Compatible:** PHP 7.4+, MySQL 5.7+, MariaDB 10.3+
