# Athlete Results System - Setup Guide

## New Setup Process

The login system has been completely recreated with a proper setup wizard that allows you to create your administrator account during installation.

## Installation Steps

### Step 1: Access the Setup Wizard

Open your browser and navigate to:
```
http://your-domain.com/setup.php
```

Or if running locally:
```
http://localhost/your-project-folder/setup.php
```

### Step 2: Database Connection

Enter your MySQL credentials:
- Database Host: Usually `localhost`
- Database Username: Your MySQL username (e.g., `root`)
- Database Password: Your MySQL password (leave empty if none)

Click "Test Connection" to verify.

### Step 3: Create Database

- Database Name: Enter a name (default: `athletes`)
- Reset existing database: Check this ONLY if you want to delete existing data

Click "Create Database"

### Step 4: Import Schema

The wizard will automatically create all necessary tables and set up the database structure.

### Step 5: Create Administrator Account

This is where you create YOUR admin account:

- Email Address: Your email (e.g., admin@yourdomain.com)
- First Name: Your first name
- Last Name: Your last name
- Password: Choose a strong password (minimum 6 characters)
- Confirm Password: Re-enter your password

Click "Create Administrator"

### Step 6: Finalization

The wizard will save your database configuration and mark installation as complete.

### Step 7: Login

You'll be redirected to the login page. Use the credentials you just created.

## What Changed?

### Old System (Broken)
- Pre-defined demo accounts with hardcoded passwords
- Password hash mismatches
- No way to create your own admin account
- Login failures with "Invalid email or password"

### New System (Working)
- Setup wizard creates YOUR admin account
- You choose your own email and password
- Password hashes are generated correctly during setup
- No demo accounts - you're in full control
- Automatic redirect to setup if not configured

## Troubleshooting

### "Database connection failed"

1. Verify MySQL is running
2. Check your credentials are correct
3. Test manually: `mysql -u root -p`

### "Failed to save configuration"

1. Check folder permissions: `chmod 755 config/`
2. Make sure config/ folder exists
3. Check if web server has write permissions

### Can't Access Setup (Redirects to Login)

Delete the lock file: `rm install/.installed`

Or add ?force to the URL: `http://your-domain.com/setup.php?force`

## Re-running Setup

If you need to start over:

1. Delete the lock file: `rm install/.installed`
2. Delete the config file (optional): `rm config/database.php`
3. Visit setup.php again

## Success!

Once you see "Setup Complete!" and can login with your credentials, you're all set!

The system is now ready to manage athletes, record results, and track performance.
