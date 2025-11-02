# GoSocially Login Issue Fix Guide

This guide helps you fix login issues with your GoSocially application when hosted on XAMMP localhost.

## 🚨 Problem: "Wrong or incorrect password/email" Error

If you're getting login errors despite setting up the database and importing SQL files, follow this comprehensive troubleshooting guide.

## 📋 Quick Setup Checklist

Before diving into debugging, make sure you've completed these basic steps:

- [ ] XAMMP Apache service is running (green status)
- [ ] XAMMP MySQL service is running (green status)
- [ ] GoSocially folder is placed in `XAMMP/htdocs/`
- [ ] Database named `gosocially` exists
- [ ] `database/schema.sql` has been imported
- [ ] `database/seed_data.sql` has been imported

## 🛠️ Debugging Tools

Your GoSocially installation now includes comprehensive debugging tools:

### 1. Setup Verification Script
**URL:** `http://localhost/GoSocially/setup_verify.php`

This is the **first tool to use**. It checks:
- ✅ XAMMP environment
- ✅ Required files presence
- ✅ Database connectivity
- ✅ Table structure
- ✅ Seed data import

### 2. Complete Debug Center
**URL:** `http://localhost/GoSocially/debug.php`

Advanced debugging interface with sections:
- **Database Test:** Connection and data verification
- **Auth Debug:** Test specific credentials
- **All Users:** View all users in database
- **Test Login:** Quick login with test accounts
- **Troubleshooting Guide:** Step-by-step instructions

### 3. Simple Database Test
**URL:** `http://localhost/GoSocially/test_db.php`

Quick database connectivity and data verification.

## 🔍 Step-by-Step Troubleshooting

### Step 1: Run Setup Verification
1. Open: `http://localhost/GoSocially/setup_verify.php`
2. Follow all 6 steps shown
3. Fix any issues identified before proceeding

### Step 2: Check Database Setup
If setup verification shows database issues:

#### Create Database:
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" in left panel
3. Enter database name: `gosocially`
4. Click "Create"

#### Import Schema:
1. In phpMyAdmin, select `gosocially` database
2. Click "Import" tab
3. Choose file: `database/schema.sql`
4. Click "Go"

#### Import Seed Data:
1. Still in `gosocially` database
2. Click "Import" tab
3. Choose file: `database/seed_data.sql`
4. Click "Go"

### Step 3: Test Login Credentials
After successful setup, try these test accounts:

| Username | Email | Password | Role |
|----------|-------|----------|------|
| admin | admin@example.com | password123 | Admin |
| moderator | moderator@example.com | password123 | Moderator |
| user1 | user1@example.com | password123 | User |
| user2 | user2@example.com | password123 | User |
| user3 | user3@example.com | password123 | User |

**You can use either the username OR email to login.**

### Step 4: Use Debug Center if Issues Persist
If login still fails:
1. Open: `http://localhost/GoSocially/debug.php`
2. Go to "Database Test" section
3. Verify all tests pass
4. Go to "All Users" section
5. Click "Test" next to any user to verify credentials
6. Check "Auth Debug" for detailed error analysis

## 🚨 Common Error Messages & Solutions

### "Database connection failed"
**Causes:**
- MySQL service not running in XAMMP
- Database `gosocially` doesn't exist
- Wrong MySQL credentials

**Solutions:**
1. Start MySQL service in XAMMP
2. Create database in phpMyAdmin
3. Check credentials in `config/database.php`

### "Invalid username or password"
**Causes:**
- No users in database
- Wrong password used
- User account disabled

**Solutions:**
1. Import `seed_data.sql`
2. Use correct password: `password123`
3. Verify users have `is_active = 1`

### "Too many attempts"
**Cause:** Rate limiting (5 attempts per 5 minutes)

**Solution:** Wait 5 minutes or clear browser cookies

### Blank page or no response
**Causes:**
- PHP errors hidden
- File permissions issues

**Solutions:**
1. Check XAMMP error logs
2. Verify file permissions
3. Use debug tools to see specific errors

## 🔧 Advanced Troubleshooting

### Check XAMMP Services
1. Open XAMMP Control Panel
2. Verify Apache is running (green)
3. Verify MySQL is running (green)
4. If not running, click "Start" for each service

### Port Conflicts
- **Apache port 80 blocked:** Change to 8080 in XAMMP config
- **MySQL port 3306 blocked:** Check for other MySQL instances

### PHP Extensions
Required extensions must be enabled:
- `pdo`
- `pdo_mysql`
- `session`
- `hash`

Check in `php.ini` and restart Apache if changed.

### File Permissions
Ensure XAMMP can read/write:
- GoSocially folder in `htdocs`
- Session save path
- Temporary directories

## 📱 Testing Your Setup

### Quick Test:
1. Open: `http://localhost/GoSocially/setup_verify.php`
2. All checks should pass
3. Note the test account credentials shown

### Login Test:
1. Go to: `http://localhost/GoSocially/`
2. Use username: `admin`
3. Use password: `password123`
4. Should redirect to `dashboard.php`

### Debug Test:
1. Open: `http://localhost/GoSocially/debug.php`
2. Go to "Test Login" section
3. Try the pre-filled test forms
4. Verify successful login

## 🎯 Expected Working Setup

When everything is working correctly:
- ✅ Setup verification shows all green checks
- ✅ Database connection test succeeds
- ✅ 5 users found in database
- ✅ Login with `admin`/`password123` works
- ✅ Successful redirect to dashboard
- ✅ No error messages

## 🆘 Still Having Issues?

If problems persist after following this guide:

1. **Check XAMMP Logs:**
   - Apache error log: `xampp/apache/logs/error.log`
   - MySQL error log: `xampp/mysql/data/mysql.err`

2. **Verify File Structure:**
   ```
   GoSocially/
   ├── config/database.php
   ├── includes/auth.php
   ├── index.php
   ├── database/schema.sql
   ├── database/seed_data.sql
   ├── setup_verify.php
   ├── debug.php
   └── test_db.php
   ```

3. **Try Different Browser:**
   - Clear cache and cookies
   - Try Chrome/Firefox/Edge

4. **Restart Everything:**
   - Stop XAMMP services
   - Wait 10 seconds
   - Start MySQL, then Apache
   - Try login again

## 📞 Additional Help

The debugging tools included in this installation provide detailed error information and step-by-step guidance. Use them in this order:

1. **setup_verify.php** - Initial setup verification
2. **debug.php** - Comprehensive debugging center
3. **test_db.php** - Quick database test

These tools will identify most common issues and provide specific solutions for your setup.

---

**Created for GoSocially XAMMP localhost installations**