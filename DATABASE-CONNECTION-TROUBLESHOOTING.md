# 🔧 Database Connection Troubleshooting Guide

## Problem
```
Database connection failed. Please contact the administrator.
```

But you've already created the database!

---

## ✅ Solution Steps

### Step 1: Update Database Credentials

The installer should have updated `config/database.php` with your credentials, but it might not have saved them correctly.

**Manual Fix:**

1. Open `config/database.php`
2. Find these lines:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');      // ← CHANGE THIS
   define('DB_USER', 'your_database_user');      // ← CHANGE THIS
   define('DB_PASS', 'your_database_password');  // ← CHANGE THIS
   ```

3. Replace with your actual credentials:
   ```php
   define('DB_HOST', 'localhost');                    // or your host
   define('DB_NAME', 'athletes');                     // Your database name
   define('DB_USER', 'root');                         // Your database user
   define('DB_PASS', 'your_password_here');           // Your database password
   ```

4. **Save the file**

---

### Step 2: Test Your Credentials

Run the diagnostic tool I've created:

**URL:** `http://your-domain.com/test-db.php`

This will show you:
- ✅ If PDO MySQL is installed
- ✅ If your credentials are correct
- ✅ If the database exists
- ✅ If the tables are loaded
- ❌ Any errors preventing connection

---

### Step 3: Find Your Database Credentials

#### If using cPanel:
1. Log in to cPanel
2. Go to **MySQL Databases**
3. Look for your database name and username
4. Copy these values to `config/database.php`

#### If using Plesk:
1. Log in to Plesk
2. Go to **Databases**
3. Click your database
4. Find the username and password
5. Update `config/database.php`

#### If using localhost/WAMP:
- DB_HOST: `localhost`
- DB_NAME: `athletes` (or whatever you named it)
- DB_USER: `root` (default)
- DB_PASS: `` (empty, or your password if you set one)

---

### Step 4: Common Issues & Fixes

#### Issue: "Connection refused"
**Cause:** MySQL server not running
**Fix:** Start MySQL/MariaDB
- **WAMP:** Click WAMP icon → MySQL → Start
- **Linux:** `sudo systemctl start mysql`
- **cPanel:** Usually runs automatically

---

#### Issue: "Access denied for user"
**Cause:** Wrong username or password
**Fix:** 
- Double-check credentials in cPanel/Plesk
- Make sure password has no special characters (or escape them properly)
- Try connecting from command line:
  ```bash
  mysql -h localhost -u your_user -p
  ```

---

#### Issue: "Unknown database 'athletes'"
**Cause:** Database doesn't exist yet
**Fix:**
1. Create the database in cPanel/Plesk
2. Or run installer again: `http://your-domain.com/installer.php`
3. Or manually import schema:
   ```bash
   mysql -u root -p athletes < install/database.sql
   ```

---

#### Issue: "SQLSTATE[HY000]: General error"
**Cause:** PHP can't connect even though credentials are right
**Fix:**
- Try connecting without specifying database first
- Check if PDO MySQL extension is enabled
- Run `/test-db.php` to see detailed error

---

### Step 5: Verify the Fix

After updating `config/database.php`:

1. **Test connection:**
   ```
   http://your-domain.com/test-db.php
   ```

2. **Check status:**
   ```
   http://your-domain.com/status.php
   ```

3. **Try accessing the system:**
   ```
   http://your-domain.com/index.php
   ```

---

## 🔍 Debug: Get More Details

If the error persists, add this to see the actual error message:

Edit `config/database.php` and change:
```php
} catch (PDOException $e) {
    error_log('Database Connection Error: ' . $e->getMessage());
    die('Database connection failed. Please contact the administrator.');
}
```

To:
```php
} catch (PDOException $e) {
    error_log('Database Connection Error: ' . $e->getMessage());
    echo '<h2>🔴 Database Error (Debug Mode)</h2>';
    echo '<p><strong>Error:</strong> ' . $e->getMessage() . '</p>';
    echo '<p><strong>Code:</strong> ' . $e->getCode() . '</p>';
    die();
}
```

This will show you the **exact error message** instead of the generic message.

---

## 📋 Verification Checklist

- [ ] Database exists in MySQL
- [ ] Database user created
- [ ] User has all privileges on database
- [ ] `DB_HOST` is set correctly
- [ ] `DB_NAME` is set correctly
- [ ] `DB_USER` is set correctly
- [ ] `DB_PASS` is set correctly (even if empty)
- [ ] `config/database.php` is saved
- [ ] MySQL server is running
- [ ] PDO MySQL extension is enabled

---

## 🆘 Still Having Issues?

Run these diagnostic commands:

**1. Check MySQL user:**
```bash
mysql -u root -p
mysql> SELECT USER();
```

**2. Check database access:**
```bash
mysql -u your_user -p your_database
```

**3. Check PHP configuration:**
```bash
php -m | grep mysql
php -m | grep pdo
```

**4. View server error logs:**
- cPanel: Home → Error Log
- Plesk: Tools → Log Files
- Linux: `/var/log/php-errors.log`

---

## 🆘 Need Help?

1. Run `http://your-domain.com/test-db.php` → screenshot the results
2. Check `config/database.php` → verify credentials
3. Check cPanel/Plesk → confirm database exists
4. Review error logs → find actual error message
5. Contact support with these details

---

**Created:** November 11, 2025
**Purpose:** Help diagnose database connection issues
**Status:** Active
