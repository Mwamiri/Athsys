# 🚀 Quick Installation Guide for Host Africa

## ✅ **What You Need:**
- Host Africa Web Hosting Basic account (or any cPanel hosting)
- Your cPanel login credentials
- 10 minutes of your time

---

## 📝 **Step-by-Step Installation**

### **Step 1: Create MySQL Database (2 minutes)**

1. **Login to cPanel** at `https://yourdomain.com:2083`

2. **Find "MySQL Databases"** (under Databases section)

3. **Create New Database:**
   - Database Name: `athletes` (or any name)
   - Click "Create Database"
   - **Write down the full name** (usually `username_athletes`)

4. **Create Database User:**
   - Username: `athleteuser` (or any name)
   - Password: Click "Generate Password" (copy it!)
   - Click "Create User"
   - **Write down username and password**

5. **Add User to Database:**
   - Select your user and database
   - Check "ALL PRIVILEGES"
   - Click "Make Changes"

✅ **Done! You now have:**
- Database name: `username_athletes`
- Database user: `username_athleteuser`
- Database password: (the one you copied)

---

### **Step 2: Upload Files (3 minutes)**

**Option A: Using File Manager (Easier)**

1. In cPanel, click **"File Manager"**

2. Navigate to `public_html`

3. Click **"Upload"** button

4. **Drag and drop** the entire `athlete-results-php` folder
   - Or click "Select File" and choose the ZIP file
   - If ZIP, right-click → Extract after upload

5. **Rename folder** (optional):
   - Right-click `athlete-results-php`
   - Rename to just `athletes` (shorter URL)

**Option B: Using FTP**

1. Open FileZilla (or any FTP client)

2. Connect to your hosting:
   - Host: `ftp.yourdomain.com`
   - Username: Your cPanel username
   - Password: Your cPanel password
   - Port: 21

3. Navigate to `/public_html/`

4. Upload the `athlete-results-php` folder

---

### **Step 3: Configure Database (2 minutes)**

1. In File Manager, navigate to:
   `public_html/athlete-results-php/config/`

2. Right-click `database.php` → **Edit**

3. **Update these lines:**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'username_athletes');      // Your database name
   define('DB_USER', 'username_athleteuser');   // Your database user
   define('DB_PASS', 'your_copied_password');   // Your database password
   ```

4. Click **"Save Changes"**

---

### **Step 4: Import Database (2 minutes)**

1. In cPanel, click **"phpMyAdmin"**

2. **Select your database** from left sidebar

3. Click **"Import"** tab at the top

4. Click **"Choose File"**

5. Navigate to: `athlete-results-php/install/database.sql`

6. Click **"Go"** at the bottom

7. Wait for: **"Import has been successfully finished"**

✅ **Database is now set up with sample data!**

---

### **Step 5: Test Your Installation (1 minute)**

1. **Open your browser**

2. **Visit:** `https://yourdomain.com/athlete-results-php/`
   - Or `https://yourdomain.com/athletes/` if you renamed it

3. **You should see the login page!** 🎉

4. **Try logging in:**
   - Email: `coach@example.com`
   - Password: `password123`

5. **Success!** You should see the dashboard

---

## 🎯 **What's Next?**

### **Immediate Actions:**

1. **Change Default Passwords:**
   - Login as admin
   - Go to Settings
   - Change password

2. **Add Your Data:**
   - Go to "Athletes" → Add your athletes
   - Go to "Competitions" → Create competitions
   - Go to "Add Data" → Start adding results

3. **Import Excel Data:**
   - Go to "Excel Import"
   - Upload your CSV files
   - Follow the import wizard

---

## 🔧 **Common Issues & Solutions**

### **Issue: "Database connection failed"**

**Solution:**
- Double-check database credentials in `config/database.php`
- Make sure database name includes your username prefix
- Verify user has ALL PRIVILEGES on the database

### **Issue: "Page not found" or "404 Error"**

**Solution:**
- Check the folder name matches your URL
- Verify files are in `public_html` not in a subfolder
- Check file permissions (should be 644 for PHP files)

### **Issue: "Permission denied"**

**Solution:**
- In File Manager, select the folder
- Click "Permissions" or "Change Permissions"
- Set folders to 755, files to 644

### **Issue: Can't login with demo accounts**

**Solution:**
- Make sure database.sql was imported successfully
- Check phpMyAdmin → users table has 3 demo users
- Try clearing browser cache and cookies

### **Issue: Blank white page**

**Solution:**
- Check PHP error logs in cPanel
- Verify PHP version is 7.4 or higher
- Check all files uploaded correctly

---

## 📞 **Need Help?**

### **Host Africa Support:**
- Website: https://hostafrica.com/support
- Email: support@hostafrica.com
- Phone: Check their website

### **cPanel Help:**
- Most cPanel installations have built-in help (? icon)
- Video tutorials available on YouTube

---

## ✅ **Installation Checklist**

- [ ] Created MySQL database in cPanel
- [ ] Created database user with password
- [ ] Added user to database with ALL PRIVILEGES
- [ ] Uploaded all files to public_html
- [ ] Edited config/database.php with correct credentials
- [ ] Imported database.sql via phpMyAdmin
- [ ] Tested login page loads
- [ ] Successfully logged in with demo account
- [ ] Changed default passwords
- [ ] System is working!

---

## 🎉 **Congratulations!**

Your Athlete Results System is now installed and ready to use!

**Your system URL:**
`https://yourdomain.com/athlete-results-php/`

**Demo Logins:**
- Coach: `coach@example.com` / `password123`
- Athlete: `athlete@example.com` / `password123`
- Admin: `admin@example.com` / `password123`

**Remember to change these passwords immediately!**

---

## 📚 **Next Steps:**

1. Read the full README.md for all features
2. Explore the dashboard
3. Add your athletes and competitions
4. Import your Excel data
5. Start tracking results!

**Enjoy your new athletics management system! 🏃‍♂️🏆**