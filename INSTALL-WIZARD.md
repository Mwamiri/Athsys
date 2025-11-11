# 🚀 Installation Guide - Athlete Results System

## Installation Methods

### **Method 1: Interactive Installer (Recommended)**

The new interactive installer provides a guided setup experience with progress tracking.

### Steps

1. **Access the Installer**

   ```text
   http://your-domain.com/installer.php
   ```

2. **Follow the Wizard Steps**

   - **Welcome** - Review installation overview
   - **Database Credentials** - Enter MySQL connection details
   - **Database Setup** - Choose database name and reset option
   - **Import Schema** - Automatically import tables and data
   - **Save Configuration** - Save your database config
   - **Complete** - Installation finished!

### Features

- ✅ Real-time progress tracking
- ✅ Database connection testing
- ✅ Option to reset existing database
- ✅ Automatic schema import
- ✅ Beautiful, responsive UI
- ✅ Installation lock (prevents re-installation)

---

### **Method 2: Manual Installation**

If you prefer manual setup:

### Step 1: Create Database

```sql
CREATE DATABASE athletes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'athlete_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON athletes.* TO 'athlete_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 2: Configure Database

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'athletes');
define('DB_USER', 'athlete_user');
define('DB_PASS', 'strong_password');
```

### Step 3: Import Schema

In phpMyAdmin:

1. Select your database
2. Click "Import" tab
3. Select `install/database.sql`
4. Click "Go"

---

## System Requirements

| Requirement | Minimum | Recommended |
|---|---|---|
| **PHP** | 7.4 | 8.1+ |
| **MySQL** | 5.7 | 8.0+ |
| **MariaDB** | 10.3 | 10.6+ |
| **Disk Space** | 50MB | 100MB+ |

---

## Troubleshooting

### Cannot Access Installer

- Check that file is in root: `/installer.php`
- Verify URL: `http://your-domain.com/installer.php`
- Check file permissions (should be 644)

### Database Connection Failed

- Verify MySQL is running
- Check credentials are correct
- Ensure user has database creation privileges
- Test connection from command line:

  ```bash
  mysql -h localhost -u username -p
  ```

### Schema Import Failed

- Ensure `install/database.sql` exists
- Check database character set is utf8mb4
- Verify database user has privileges
- Check error logs for details

### Cannot Write Configuration

- Check `config/` directory permissions (755)
- Ensure PHP has write access
- Try creating a test file manually

### Installation Already Complete?

To re-run installation:

```text
http://your-domain.com/installer.php?force=1
```

---

## After Installation

1. **Delete or Rename Installer**

   ```bash
   # Delete
   rm installer.php install/api.php

   # Or rename
   mv installer.php installer.php.bak
   ```

2. **Secure Your Installation**
   - Change default admin password
   - Set proper file permissions
   - Configure backup strategy

3. **Configure Application**
   - Update `config/app.php` settings
   - Configure email (if needed)
   - Set up scheduled tasks

---

## What Gets Installed?

The installer creates:

- ✅ Database with proper character encoding
- ✅ User tables
- ✅ Athlete records table
- ✅ Results and scores
- ✅ Admin users
- ✅ System settings

---

## Support

For issues:

1. Check the error message carefully
2. Review server error logs
3. Verify all requirements are met
4. Try manual installation method

---

## Installation Lock

After successful installation, a lock file is created:

```text
install/.installed
```

This prevents accidental re-installation. To reset, delete this file.

---

**Last Updated:** November 11, 2025
