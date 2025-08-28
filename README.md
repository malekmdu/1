# GeoPortfolio Pro - PHP Version

A modern, dynamic portfolio website for GIS & Remote Sensing professionals. This is a complete conversion from React/TypeScript to PHP with MySQL database.

## Features

### Frontend Features
- **Responsive Design**: Mobile-first responsive design using Bootstrap 5
- **Modern UI**: Clean, professional interface with gradient themes
- **Portfolio Showcase**: Display works/projects with categories, images, and detailed descriptions
- **Blog System**: Full-featured blog with categories and rich content
- **Contact Form**: Professional contact form with message management
- **About Page**: Comprehensive profile, skills, education, and experience display

### Backend Features
- **Admin Dashboard**: Complete content management system
- **User Management**: Role-based access (Admin/Editor) with user controls
- **Content Management**: Full CRUD operations for works, blog posts, and profile data
- **Skills Management**: Dynamic skills with progress bars and categories
- **Message System**: Contact form submissions with read/unread status
- **Settings Management**: Site configuration, social links, and email settings
- **Activity Logging**: Login activity and email logs
- **Data Import/Export**: Backup and restore functionality

### Technical Features
- **PHP 8+ Compatible**: Modern PHP with PDO database connections
- **MySQL Database**: Structured database with proper relationships
- **Security**: Password hashing, CSRF protection, and input sanitization
- **MVC Architecture**: Clean separation of concerns
- **Responsive Admin**: Mobile-friendly admin panel
- **File Uploads**: Image upload and management system

## Installation

### Requirements
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- mod_rewrite enabled (for clean URLs)

### Setup Instructions

1. **Database Setup**
   ```bash
   # Create the database and import the schema
   mysql -u root -p < sql/database_schema.sql
   
   # Import sample data (optional)
   mysql -u root -p < sql/sample_data.sql
   ```

2. **Configuration**
   - Edit `config/database.php` with your database credentials
   - Update the database connection settings:
   ```php
   private $host = 'localhost';
   private $database = 'geoportfolio_pro';
   private $username = 'your_username';
   private $password = 'your_password';
   ```

3. **File Permissions**
   ```bash
   # Make assets directory writable for uploads
   chmod 755 assets/
   chmod 755 assets/images/
   ```

4. **Web Server Configuration**
   
   **Apache (.htaccess)**
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^([^/]+)/?$ $1.php [L,QSA]
   ```

   **Nginx**
   ```nginx
   location / {
       try_files $uri $uri/ $uri.php?$query_string;
   }
   ```

## Default Login Credentials

After installation, you can log in with these default credentials:

- **Admin User**: admin@example.com / admin
- **Editor User**: editor@example.com / editor

**Important**: Change these passwords immediately after installation!

## File Structure

```
geoportfolio-php/
├── admin/                  # Admin panel pages
│   ├── index.php          # Dashboard
│   ├── works.php          # Works management
│   ├── blog.php           # Blog management
│   ├── messages.php       # Message management
│   ├── users.php          # User management (Admin only)
│   └── settings.php       # Site settings (Admin only)
├── api/                   # API endpoints
├── assets/               # Static assets
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript files
│   └── images/          # Uploaded images
├── config/              # Configuration files
│   └── database.php     # Database configuration
├── includes/            # PHP includes
│   ├── auth.php        # Authentication system
│   ├── functions.php   # Helper functions
│   └── models.php      # Data models
├── sql/                # Database files
│   ├── database_schema.sql  # Database structure
│   └── sample_data.sql     # Sample data
├── templates/          # Reusable templates
│   ├── navbar.php     # Navigation bar
│   └── footer.php     # Footer
├── index.php          # Home page
├── about.php          # About page
├── works.php          # Works listing
├── work-detail.php    # Individual work page
├── blog.php           # Blog listing
├── blog-detail.php    # Individual blog post
├── contact.php        # Contact page
├── login.php          # Login page
└── logout.php         # Logout handler
```

## Database Schema

The system uses the following main tables:
- `users` - User accounts and authentication
- `works` - Portfolio projects/works
- `blog_posts` - Blog articles
- `categories` - Work and blog categories
- `profile_data` - Profile information
- `skills` - Skills with progress bars
- `messages` - Contact form submissions
- `site_settings` - Configuration settings

## Customization

### Themes and Styling
- Edit `assets/css/style.css` for custom styling
- Modify CSS variables in `:root` for color schemes
- Bootstrap 5 classes available throughout

### Adding New Features
- Create new models in `includes/models.php`
- Add admin pages in `admin/` directory
- Create API endpoints in `api/` directory

### Email Configuration
- Configure SMTP settings in admin panel
- Email logs stored in `email_log` table
- Customize email templates in relevant PHP files

## Security Features

- **Password Hashing**: BCrypt password hashing
- **CSRF Protection**: Token-based CSRF protection
- **Input Sanitization**: All inputs sanitized and validated
- **SQL Injection Prevention**: Prepared statements throughout
- **Session Management**: Secure session handling
- **Role-based Access**: Admin/Editor role separation

## Backup and Restore

### Database Backup
```bash
mysqldump -u username -p geoportfolio_pro > backup.sql
```

### Database Restore
```bash
mysql -u username -p geoportfolio_pro < backup.sql
```

### File Backup
Backup the entire project directory, especially:
- `assets/images/` (uploaded files)
- `config/` (configuration)
- Database dump

## Support

For issues and questions:
1. Check the error logs in your web server
2. Verify database connections in `config/database.php`
3. Ensure proper file permissions
4. Check PHP version compatibility (PHP 8.0+)

## License

This project is a converted version of the original React-based GeoPortfolio Pro. All features and functionality have been maintained while adapting to PHP/MySQL architecture.