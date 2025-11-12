# 🚀 Setup Required - Cannot Login or Access Dashboard

## What's Happening?

If you're seeing this message or being redirected to `setup.php`, it means your Athlete Results System needs to be configured before you can use it.

## Why Can't I Login?

The system requires two things before login will work:

1. **Database Configuration** (`config/database.php`) - Connection details for your MySQL database
2. **Installation Lock File** (`install/.installed`) - Confirmation that setup is complete

If either of these is missing, the system will redirect you to the setup wizard.

---

## 🛠️ Quick Fix: Run the Setup Wizard

### Step 1: Access the Setup Wizard

Visit: `http://yourdomain.com/setup.php`

### Step 2: Complete All Steps

The wizard will guide you through:

1. **Database Connection** - Enter your MySQL credentials
   - Host: Usually `localhost`
   - Database: Your database name (e.g., `athletes`)
   - Username: Your database username (e.g., `root` or from cPanel)
   - Password: Your database password

2. **Create Database** - Let the wizard create your database
   - Or create it manually in cPanel/phpMyAdmin first

3. **Import Schema** - Creates all necessary tables
   - This may take 30-60 seconds
   - Wait for 100% completion

4. **Create Admin Account** - Set up your administrator login
   - Email: Your admin email
   - Password: Choose a strong password (min 6 characters)
   - Name: Your first and last name

5. **Save Configuration** - Writes `config/database.php` file
   - Creates `.installed` lock file
   - Redirects to login page

### Step 3: Login

After setup completes:
- Visit: `http://yourdomain.com/login.php`
- Use the admin credentials you created
- You should now access the dashboard!

---

## 🔧 Alternative: Manual Setup

If you prefer to set up manually or the wizard isn't working:

### 1. Create Database

In cPanel or phpMyAdmin:
```sql
CREATE DATABASE athletes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Import Schema

In phpMyAdmin:
- Select your database
- Click "Import"
- Choose file: `install/database.sql`
- Click "Go"

### 3. Create Database Configuration

Copy `config/database.example.php` to `config/database.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'athletes');           // Your database name
define('DB_USER', 'root');              // Your database username
define('DB_PASS', 'your_password');     // Your database password
define('DB_CHARSET', 'utf8mb4');

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log('Database Connection Error: ' . $e->getMessage());
    die('Database connection failed. Please contact the administrator.');
}

function getDB() {
    global $pdo;
    return $pdo;
}
?>
```

### 4. Create Admin User

Run this SQL in phpMyAdmin:

```sql
INSERT INTO users (email, password_hash, first_name, last_name, role, is_active)
VALUES (
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- password: password123
    'Admin',
    'User',
    'administrator',
    1
);
```

### 5. Create Installation Lock File

Create file: `install/.installed` with this content:
```json
{"installed_at":"2025-11-12 18:00:00","database":"athletes","version":"1.0.0"}
```

### 6. Login

- Visit: `http://yourdomain.com/login.php`
- Email: `admin@example.com`
- Password: `password123`
- **Change this password immediately!**

---

## 🆘 Troubleshooting

### Error: "Database connection failed"

**Cause:** Wrong database credentials or MySQL not running

**Fix:**
1. Check credentials in `config/database.php`
2. Verify MySQL/MariaDB is running
3. Test connection: Visit `http://yourdomain.com/test-db.php`

### Error: "Invalid email or password"

**Cause:** No admin user created yet

**Fix:**
1. Run `check-users.php` to see if users exist
2. If no users, complete Step 3 of setup wizard (Import Schema)
3. Or manually run the SQL in step 4 above

### Setup Wizard Keeps Redirecting

**Cause:** Installation already marked as complete

**Fix:**
1. Delete `install/.installed` file
2. Refresh and try setup wizard again
3. Or visit: `http://yourdomain.com/setup.php?force=1`

### Still Can't Login After Setup

**Fix:**
1. Clear browser cache (Ctrl+Shift+Delete)
2. Try incognito/private browsing mode
3. Check `login-troubleshooting.php` for diagnostics
4. Review server error logs

---

## 📋 Verification Checklist

Before you can login, verify:

- [ ] MySQL/MariaDB server is running
- [ ] Database exists and has tables
- [ ] `config/database.php` file exists with correct credentials
- [ ] `install/.installed` file exists
- [ ] At least one admin user exists in database
- [ ] No PHP errors in server logs

---

## 🔐 Security Notes

- The `config/database.php` file contains sensitive credentials
- It's automatically excluded from Git (in `.gitignore`)
- Never commit this file to version control
- Change default passwords immediately after first login
- Use strong, unique passwords for production

---

## 📞 Need More Help?

1. **Check existing documentation:**
   - `README.md` - General installation guide
   - `LOGIN-TROUBLESHOOTING.md` - Login-specific issues
   - `DATABASE-CONNECTION-TROUBLESHOOTING.md` - Database issues

2. **Run diagnostics:**
   - `test-db.php` - Test database connection
   - `login-troubleshooting.php` - Check user accounts
   - `status.php` - System status overview

3. **Common issues:**
   - Database not created → Use cPanel or setup wizard
   - Wrong credentials → Check `config/database.php`
   - No users in database → Run schema import again
   - Sessions not working → Check PHP session settings

---

**Last Updated:** November 12, 2025  
**Purpose:** Guide users through required setup before login  
**Status:** Active
