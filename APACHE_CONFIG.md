# Apache Configuration Guide

## Problem
You're getting "Not Found" error when accessing `http://localhost/backend/admin/dashboard.php`

## Solution Options

### Option 1: Quick Fix (Recommended)
Access the admin panel using the full path:
```
http://localhost/Portfolio/backend/admin/test.php
```

### Option 2: Configure Apache Document Root

#### Step 1: Open XAMPP Control Panel
1. Start XAMPP Control Panel
2. Click "Config" next to Apache
3. Select "Apache (httpd.conf)"

#### Step 2: Update Document Root
Find this line in httpd.conf:
```apache
DocumentRoot "c:/xampp/htdocs"
```

Change it to:
```apache
DocumentRoot "c:/xampp/htdocs/Portfolio/backend"
```

Also find this section:
```apache
<Directory "c:/xampp/htdocs">
```

Change it to:
```apache
<Directory "c:/xampp/htdocs/Portfolio/backend">
```

#### Step 3: Restart Apache
1. Stop Apache in XAMPP
2. Start Apache again

#### Step 4: Test Access
Now you can access:
```
http://localhost/admin/dashboard.php
```

### Option 3: Virtual Host (Advanced)

Add this to your httpd-vhosts.conf file:
```apache
<VirtualHost *:80>
    DocumentRoot "c:/xampp/htdocs/Portfolio/backend"
    ServerName localhost
    <Directory "c:/xampp/htdocs/Portfolio/backend">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## Testing

### Test 1: Basic PHP Test
Access: `http://localhost/Portfolio/backend/admin/test.php`

You should see:
- PHP working confirmation
- Database connection status
- List of admin pages

### Test 2: Login Page
Access: `http://localhost/Portfolio/backend/admin/login.php`

Default credentials:
- Username: `admin`
- Password: `admin123`

### Test 3: Dashboard
After successful login, you'll be redirected to the dashboard.

## Troubleshooting

### If test.php works but other pages don't:
1. Check file permissions
2. Verify .htaccess is working
3. Check Apache error logs

### If nothing works:
1. Verify XAMPP Apache is running
2. Check that PHP module is enabled
3. Verify file paths are correct

### Common Issues:
- **404 Not Found**: Apache not pointing to correct directory
- **403 Forbidden**: File permissions issue
- **500 Internal Server Error**: PHP syntax error or .htaccess issue

## Recommended Approach

For now, use the full path approach:
```
http://localhost/Portfolio/backend/admin/login.php
```

This works without any Apache configuration changes and is the most reliable method.
