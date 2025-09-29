# Pokemon Management System - Sample Configuration

## Apache .htaccess (if needed)
# Place this content in .htaccess in the root directory

```apache
RewriteEngine On

# Redirect HTTP to HTTPS (optional)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Cache static assets
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
</IfModule>
```

## Nginx Configuration Sample
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/pokemon-management-system;
    index index.php;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security
    location ~ /\. {
        deny all;
    }

    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## Environment Variables (Optional)
Create a .env file (not included in git):

```env
DB_HOST=localhost
DB_NAME=pokemon_management
DB_USER=your_username
DB_PASS=your_password
```

## PHP Configuration Recommendations
Ensure these settings in php.ini:

```ini
post_max_size = 16M
upload_max_filesize = 16M
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_only_cookies = 1
memory_limit = 256M
```