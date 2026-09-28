# Scholar SMS - Setup Guide

## Step 1 - Copy folder
Extract this whole `scholar_sms` folder into:
```
C:\xampp\htdocs\scholar_sms
```
(Delete any old project folders like "school system", "school management" etc, to avoid confusion.)

## Step 2 - Import database
1. Start XAMPP (Apache + MySQL).
2. Open http://localhost/phpmyadmin
3. Click "New" and DO NOT create the database manually - just click the **SQL tab** on the server level (not inside a database) and paste the contents of `sql/scholar_sms.sql`, then click Go.
   (This script creates a fresh `scholar_sms` database with all tables automatically.)

## Step 3 - Open the app
Go to: http://localhost/scholar_sms

## Step 4 - Login
```
Username: admin
Password: admin123
```

## Notes
- If your MySQL has a root password set, edit `config/db.php` and put it in the `$pass` variable.
- All modules (Students, Teachers, Parents, Activities, Assignments, Payments) have full Add / Edit / Delete / List pages.
- Passwords are stored securely using PHP's `password_hash()` / `password_verify()`.
