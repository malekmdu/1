# GeoPortfolio Pro - Installation Guide

## Quick Installation

### Method 1: Web-Based Installer (Recommended)

1. **Upload Files**
   - Upload all files to your web server
   - Ensure the `config/` directory is writable

2. **Run Installer**
   - Navigate to `http://yoursite.com/install.php`
   - Follow the step-by-step installation wizard

3. **Complete Setup**
   - The installer will guide you through:
     - Database configuration
     - Database schema import
     - Admin user creation
     - Site configuration

4. **Security**
   - Delete `install.php` after installation
   - Ensure proper file permissions

### Method 2: Manual Installation

#### Step 1: Database Setup
```sql
-- Create database
CREATE DATABASE geoportfolio_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Import schema
mysql -u username -p geoportfolio_pro < sql/database_schema.sql

-- Import sample data (optional)
mysql -u username -p geoportfolio_pro < sql/sample_data.sql
```

#### Step 2: Configuration
1. Copy `config/database.php.example` to `config/database.php`
2. Update database credentials:
```php
private $host = 'localhost';
private $database = 'geoportfolio_pro';
private $username = 'your_username';
private $password = 'your_password';
```

#### Step 3: File Permissions
```bash
chmod 755 assets/
chmod 755 assets/images/
chmod 644 config/database.php
```

#### Step 4: Create Admin User
Access `http://yoursite.com/admin/` and use default credentials:
- **Email:** admin@example.com
- **Password:** admin

**Important:** Change these credentials immediately!

## Server Requirements

### Minimum Requirements
- **PHP:** 8.0 or higher
- **MySQL:** 5.7 or higher (or MariaDB 10.2+)
- **Web Server:** Apache or Nginx
- **Extensions:** PDO, PDO_MySQL, mbstring, openssl

### Recommended
- **PHP:** 8.1+
- **MySQL:** 8.0+
- **Memory:** 128MB+
- **mod_rewrite** enabled (Apache)

## Web Server Configuration

### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([^/]+)/?$ $1.php [L,QSA]

# Security headers
Header always set X-Frame-Options DENY
Header always set X-Content-Type-Options nosniff
Header always set X-XSS-Protection "1; mode=block"
```

### Nginx
```nginx
server {
    listen 80;
    server_name yoursite.com;
    root /path/to/geoportfolio-php;
    index index.php;

    location / {
        try_files $uri $uri/ $uri.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security
    location ~ /\. {
        deny all;
    }
    
    location ~* \.(sql|md|txt|log)$ {
        deny all;
    }
}
```

## Post-Installation Setup

### 1. Admin Panel Access
- URL: `http://yoursite.com/admin/`
- Default credentials (if using sample data):
  - Admin: admin@example.com / admin
  - Editor: editor@example.com / editor

### 2. Essential Configuration
1. **Profile Setup** - Add your personal information
2. **Site Settings** - Configure site title, social links
3. **Categories** - Create work and blog categories
4. **Skills** - Add your technical skills
5. **Content** - Add your works and blog posts

### 3. Security Checklist
- [ ] Change default admin password
- [ ] Delete `install.php`
- [ ] Set proper file permissions
- [ ] Configure HTTPS
- [ ] Set up regular backups
- [ ] Update contact email in settings

## Troubleshooting

### Common Issues

#### Database Connection Error
- Verify database credentials in `config/database.php`
- Ensure MySQL service is running
- Check database user permissions

#### File Permission Issues
```bash
# Fix common permission issues
chmod 755 assets/
chmod 755 assets/images/
chmod 644 config/database.php
chown -R www-data:www-data /path/to/geoportfolio-php
```

#### 404 Errors
- Enable mod_rewrite (Apache)
- Check .htaccess file exists
- Verify web server configuration

#### PHP Errors
- Check PHP version (8.0+ required)
- Enable required extensions
- Check PHP error logs

### Getting Help

1. **Check Logs**
   - Web server error logs
   - PHP error logs
   - Browser console

2. **Verify Requirements**
   - PHP version and extensions
   - Database connectivity
   - File permissions

3. **Test Installation**
   - Access homepage: `http://yoursite.com/`
   - Access admin: `http://yoursite.com/admin/`
   - Test contact form

## Backup and Maintenance

### Database Backup
```bash
# Create backup
mysqldump -u username -p geoportfolio_pro > backup_$(date +%Y%m%d).sql

# Restore backup
mysql -u username -p geoportfolio_pro < backup_20240101.sql
```

### File Backup
```bash
# Backup entire installation
tar -czf geoportfolio_backup_$(date +%Y%m%d).tar.gz /path/to/geoportfolio-php
```

### Regular Maintenance
- Update PHP and MySQL regularly
- Monitor disk space and performance
- Review security logs
- Test backup restoration
- Update content regularly

## Support

For additional support:
1. Check the README.md file
2. Review error logs
3. Verify server requirements
4. Test with sample data

---

**Note:** This installation guide assumes basic knowledge of web server administration. If you're not comfortable with server configuration, consider asking your hosting provider for assistance.