# 🔐 Login Issues Troubleshooting Guide

## ❌ Why Can't You Login?

You mentioned you can't login to **any of the three** pages:
- `index.php` (requires login)
- `installer-menilo.php` (setup wizard - should work)
- `index-menilo.php` (dashboard - requires login)

Let me help you diagnose this.

---

## 🔍 Quick Diagnosis

### Run This First

Visit this diagnostic page:
```
http://your-domain.com/login-troubleshooting.php
```

This will check:
- ✅ If users table exists
- ✅ If demo users are created
- ✅ If you can login with demo credentials
- ❌ What's preventing login

---

## 🎯 Most Common Causes (In Order)

### Problem #1: Schema NOT Imported ⚠️ **Most Likely**

**Symptoms:**
- "Invalid email or password" even with correct credentials
- Demo users don't exist in database
- Login troubleshooting shows "0 users"

**Solution:**

**Step 1:** Run the installer to import schema
```
http://your-domain.com/installer.php
```

**Step 2:** Go to Step 4: "Import Database Schema"
- Wait for progress bar to complete (30-60 seconds)
- Look for ✅ success message

**Step 3:** Return to login troubleshooting
```
http://your-domain.com/login-troubleshooting.php
```

**Step 4:** Verify demo users now exist
- Should show 3 users: admin, coach, athlete
- "Can Login" should show ✅ Yes

---

### Problem #2: Wrong Database Credentials

**Symptoms:**
- "Database connection failed" error on any page
- Error on login troubleshooting page
- Can't access any system page

**Solution:**

1. Open `config/database.php`
2. Verify your credentials are **exactly correct**:
   ```php
   define('DB_HOST', 'localhost');     // Your host
   define('DB_NAME', 'athletes');      // Your database name
   define('DB_USER', 'root');          // Your database username  
   define('DB_PASS', '');              // Your database password
   ```

3. Test using `test-db.php`:
   ```
   http://your-domain.com/test-db.php
   ```

4. If connection fails, follow the troubleshooting steps:
   - Is MySQL running?
   - Are credentials correct?
   - Does database exist?

---

### Problem #3: MySQL Server Not Running

**Symptoms:**
- "Connection refused" errors
- Can't connect even with correct credentials
- "Database connection failed" on all pages

**Solution:**

**If using WAMP (Windows):**
1. Click WAMP icon in system tray
2. Hover over "MySQL"
3. Click "Start MySQL"
4. Wait for it to turn green
5. Try login again

**If using Linux/cPanel:**
1. SSH into your server
2. Run: `sudo systemctl start mysql`
3. Try login again

**If using Plesk/hosting:**
- MySQL should auto-start
- Contact your hosting provider

---

### Problem #4: Browser Cache Issues

**Symptoms:**
- You fixed the problem but still can't login
- Error messages seem outdated
- Demo credentials don't work even though database shows they should

**Solution:**

1. **Clear Browser Cache:**
   - Chrome: `Ctrl+Shift+Delete`
   - Firefox: `Ctrl+Shift+Delete`
   - Edge: `Ctrl+Shift+Delete`
   - Safari: `Cmd+Y` then "Clear History"

2. **Try Incognito/Private Mode:**
   - Chrome: `Ctrl+Shift+N`
   - Firefox: `Ctrl+Shift+P`
   - Edge: `Ctrl+Shift+InPrivate`

3. **Clear Browser Cookies:**
   - Right-click page → Inspect → Storage → Cookies
   - Delete all cookies for your domain

---

### Problem #5: Session Configuration Issue

**Symptoms:**
- Login appears to work but redirects back to login
- Session seems to not save
- "Invalid email or password" with correct credentials

**Solution:**

1. Check if sessions are enabled in `config/database.php`
   - Should have: `session_start();` at the very top

2. Verify session save path exists:
   - `/tmp` on Linux
   - `C:\Windows\Temp` on Windows

3. Test session handler in a file:
   ```php
   <?php
   session_start();
   $_SESSION['test'] = 'works';
   echo $_SESSION['test'] ? 'Sessions work!' : 'Sessions broken!';
   ```

---

## ✅ Step-by-Step Login Fix

### If You've Just Installed:

**Step 1: Verify Installation Completed**
```
http://your-domain.com/installer.php
```
- All 6 steps should show ✅ Complete
- If any step shows ❌, redo that step

**Step 2: Check Schema Was Imported**
```
http://your-domain.com/login-troubleshooting.php
```
- Should show 3+ users
- All should have "Can Login: ✅ Yes"

**Step 3: Try Demo Login**
Use these credentials:
- **Email:** `admin@example.com`
- **Password:** `password123`

**Step 4: If Still Doesn't Work**
- Run `/test-db.php` to verify connection
- Check browser console (F12) for errors
- Review server error logs

---

## 🔑 Default Demo Credentials

These credentials are created during installation:

| Role | Email | Password | Database |
|------|-------|----------|----------|
| Admin | `admin@example.com` | `password123` | users table |
| Coach | `coach@example.com` | `password123` | users table |
| Athlete | `athlete@example.com` | `password123` | users table |

**⚠️ Important:** Change these passwords immediately after login!

---

## 🆘 Advanced Troubleshooting

### Check Session is Saving

Create `test-session.php`:
```php
<?php
session_start();
$_SESSION['test_value'] = 'This is a test';

echo 'Session ID: ' . session_id() . '<br>';
echo 'Session Data: ' . $_SESSION['test_value'] . '<br>';
echo 'Session Save Path: ' . session_save_path() . '<br>';
?>
```

Visit: `http://your-domain.com/test-session.php`

---

### Debug Login Process

Edit `login.php` temporarily (lines 14-31):
```php
// Add this for debugging
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>";
    echo "Email submitted: " . $_POST['email'] . "\n";
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "User found in database\n";
        echo "Password hash in DB: " . substr($user['password_hash'], 0, 20) . "...\n";
        
        $verify = password_verify($_POST['password'], $user['password_hash']);
        echo "Password verification result: " . ($verify ? 'PASS' : 'FAIL') . "\n";
    } else {
        echo "User NOT found in database\n";
    }
    echo "</pre>";
    // REMOVE THIS DEBUG CODE AFTER TESTING
}
```

Then:
1. Visit login page
2. Submit credentials
3. See exactly what's happening

---

## 📋 Complete Verification Checklist

- [ ] MySQL server is running
- [ ] Database exists: `athletes`
- [ ] Users table exists (check with login-troubleshooting.php)
- [ ] Demo users exist (3+ rows in users table)
- [ ] `config/database.php` has correct credentials
- [ ] Can connect using `test-db.php`
- [ ] Browser cache cleared
- [ ] Tried incognito/private mode
- [ ] Using correct email and password
- [ ] `is_active` field is 1 for demo users
- [ ] No PHP errors in server error log

---

## 🆘 Still Can't Login?

1. **Run diagnostics:**
   - `/test-db.php` - Check database connection
   - `/login-troubleshooting.php` - Check users table
   - `/status.php` - Check installation status

2. **Take screenshots** of:
   - The error message you're getting
   - Output from `/login-troubleshooting.php`
   - Output from `/test-db.php`

3. **Gather this info:**
   - What page won't let you login?
   - What error message do you see?
   - Did you complete the installer?
   - Are demo users showing in login-troubleshooting.php?

4. **Share with support:**
   - Error message (exact text)
   - Screenshots from diagnostic pages
   - Your system info (PHP version, MySQL version)

---

## 🎯 If Multiple Users Can't Login

### The Issue: Users Table Doesn't Have Any Users

**Why This Happens:**
- Installation wizard didn't complete Step 4 (Import Schema)
- Schema import timed out
- Database error during import

**The Fix:**
1. Open installer: `http://your-domain.com/installer.php`
2. Skip to Step 4: "Import Database Schema"
3. Let it run completely (30-60 seconds)
4. Wait for 100% completion
5. Check for success message
6. Try login again

---

## 📞 Support Information

**If you get these exact errors:**

| Error | Cause | Fix |
|-------|-------|-----|
| "Database connection failed" | No connection | Check credentials in config/database.php |
| "Invalid email or password" | No users in DB | Run installer Step 4 again |
| "Session error" | Sessions disabled | Check session settings |
| "Blank login page" | PHP error | Check server error logs |
| "403 Forbidden" | File permissions | Set 644 on .php files, 755 on folders |

---

**Created:** November 12, 2025  
**Purpose:** Help diagnose and fix login issues  
**Status:** Active
