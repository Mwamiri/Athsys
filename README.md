# 🏃‍♂️ Athlete Results System - PHP Version for Shared Hosting

## ✅ **Perfect for Host Africa Web Hosting Basic Package**

This is a complete PHP/MySQL version of the Athlete Results System designed specifically for shared hosting environments like Host Africa's Web Hosting Basic package.

---

## 📋 **System Requirements**

✅ PHP 7.4 or higher (PHP 8.x recommended)  
✅ MySQL 5.7 or higher / MariaDB 10.3+  
✅ Apache with mod_rewrite (standard on cPanel)  
✅ 50MB+ disk space  
✅ cPanel or similar control panel  

**These are standard on Host Africa Web Hosting Basic!**

---

## 🚀 **Installation Steps**

### **Step 1: Create MySQL Database in cPanel**

1. Log into your cPanel
2. Go to **MySQL Databases**
3. Create a new database (e.g., `youruser_athletes`)
4. Create a database user with a strong password
5. Add the user to the database with **ALL PRIVILEGES**
6. **Note down**: Database name, username, and password

### **Step 2: Upload Files**

1. **Using File Manager:**
   - Log into cPanel → File Manager
   - Navigate to `public_html` (or your domain folder)
   - Create folder: `athlete-results` (or any name you prefer)
   - Upload all files from `athlete-results-php` folder

2. **Using FTP:**
   - Connect via FTP (FileZilla, etc.)
   - Upload to `/public_html/athlete-results/`

### **Step 3: Configure Database**

1. Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'youruser_athletes');  // Your database name
   define('DB_USER', 'youruser_dbuser');    // Your database username
   define('DB_PASS', 'your_password');      // Your database password
   ```

### **Step 4: Import Database**

1. Go to cPanel → **phpMyAdmin**
2. Select your database
3. Click **Import** tab
4. Choose file: `install/database.sql`
5. Click **Go**
6. Wait for "Import has been successfully finished"

### **Step 5: Set Permissions**

In File Manager, set these permissions:
- `config/` folder: 755
- `uploads/` folder: 777 (if you add file upload)
- All `.php` files: 644

### **Step 6: Access Your System**

Visit: `https://yourdomain.com/athlete-results/`

**Demo Login Credentials:**
- **Coach**: `coach@example.com` / `password123`
- **Athlete**: `athlete@example.com` / `password123`
- **Admin**: `admin@example.com` / `password123`

---

## 🎨 **Features Included**

### ✅ **Core Features:**
- ✅ User authentication with role-based access
- ✅ Dashboard with statistics
- ✅ Athlete management
- ✅ Results tracking
- ✅ Competition management
- ✅ Rankings and leaderboards
- ✅ Personal record tracking
- ✅ Season best detection
- ✅ Excel/CSV import
- ✅ Reports generation
- ✅ Responsive design (mobile-friendly)

### 👥 **User Roles:**
- **Coach**: Full access to manage athletes, results, competitions
- **Athlete**: View personal results and rankings
- **Administrator**: Full system access

---

## 📁 **File Structure**

```
athlete-results-php/
├── index.php              # Dashboard
├── login.php              # Login page
├── logout.php             # Logout handler
├── results.php            # Results management
├── athletes.php           # Athletes management
├── competitions.php       # Competitions management
├── rankings.php           # Rankings and leaderboards
├── analytics.php          # Performance analytics
├── add-data.php           # Add new data
├── excel-import.php       # Excel/CSV import
├── reports.php            # Generate reports
├── settings.php           # User settings
├── config/
│   └── database.php       # Database configuration
├── includes/
│   ├── header.php         # Common header
│   ├── footer.php         # Common footer
│   └── functions.php      # Helper functions
├── assets/
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   ├── js/
│   │   └── main.js        # JavaScript functions
│   └── images/            # Images and icons
├── install/
│   └── database.sql       # Database schema
└── README.md              # This file
```

---

## 🔧 **Configuration**

### **Change Site Settings**

Edit `config/database.php` for database settings.

### **Change Password**

To change demo user passwords:
1. Go to phpMyAdmin
2. Select your database
3. Browse `users` table
4. Edit password_hash field
5. Use online bcrypt generator for new password

Or use the Settings page after logging in.

---

## 📊 **Excel/CSV Import**

### **Supported Formats:**

**Athletes CSV:**
```csv
firstName,lastName,dateOfBirth,gender,category
John,Kipchoge,1995-05-15,M,Senior
Mary,Wanjiku,1998-08-22,F,Senior
```

**Results CSV:**
```csv
athleteName,eventName,performance,unit,competitionName,competitionDate,placement
John Kipchoge,100m Sprint,10.25,seconds,National Championships,2024-06-15,1
```

### **Import Steps:**
1. Log in as Coach or Admin
2. Go to **Excel Import** page
3. Choose CSV file
4. Click **Import**
5. Review results

---

## 🔒 **Security Features**

✅ Password hashing (bcrypt)  
✅ SQL injection prevention (prepared statements)  
✅ XSS protection (input sanitization)  
✅ Session management  
✅ Role-based access control  
✅ CSRF protection (can be added)  

---

## 🌐 **Browser Support**

✅ Chrome/Edge (latest)  
✅ Firefox (latest)  
✅ Safari (latest)  
✅ Mobile browsers (iOS/Android)  

---

## 📱 **Mobile Responsive**

The system is fully responsive and works perfectly on:
- Desktop computers
- Tablets
- Smartphones

---

## 🆘 **Troubleshooting**

### **"Database connection failed"**
- Check database credentials in `config/database.php`
- Verify database exists in cPanel
- Check user has privileges

### **"Page not found" errors**
- Check .htaccess file exists
- Verify mod_rewrite is enabled
- Check file permissions

### **"Permission denied" errors**
- Set correct file permissions (644 for files, 755 for folders)
- Check folder ownership

### **Can't login**
- Verify database was imported correctly
- Check users table has demo accounts
- Clear browser cache and cookies

---

## 🔄 **Updating**

To update the system:
1. Backup your database (phpMyAdmin → Export)
2. Backup your files
3. Upload new files (don't overwrite config/database.php)
4. Run any new SQL updates

---

## 💾 **Backup**

### **Regular Backups:**
1. **Database**: cPanel → phpMyAdmin → Export
2. **Files**: cPanel → File Manager → Compress → Download

### **Recommended Schedule:**
- Daily: Automatic cPanel backups (if available)
- Weekly: Manual database export
- Monthly: Full file backup

---

## 🎯 **Next Steps**

1. ✅ Change default passwords
2. ✅ Add your athletes
3. ✅ Create competitions
4. ✅ Import existing Excel data
5. ✅ Start recording results
6. ✅ Generate rankings

---

## 📞 **Support**

For Host Africa specific issues:
- Visit: https://hostafrica.com/support
- Email: support@hostafrica.com

For system issues:
- Check this README
- Review error logs in cPanel

---

## 📄 **License**

This system is provided as-is for athletics management purposes.

---

## ✨ **Features Coming Soon**

- 📧 Email notifications
- 📱 Mobile app
- 📊 Advanced analytics
- 🏆 Certificates generation
- 📸 Photo uploads
- 💬 Comments system

---

**Built specifically for Host Africa shared hosting! 🇰🇪**