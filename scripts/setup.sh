#!/usr/bin/env bash

if [[ $1 == 'local' ]]; then
    echo "Creating self-signed SSL certificate for HTTPS access"
    mkdir /etc/nginx/ssl
    openssl req \
        -new \
        -newkey rsa:2048 \
        -days 365 \
        -nodes \
        -x509 \
        -subj "/C=FR/ST=Essonne/L=Palaiseau/O=GroupEat/CN=$2" \
        -keyout /etc/nginx/ssl/nginx.key \
        -out /etc/nginx/ssl/nginx.crt
fi

block="# Don't send the Nginx version number in error pages and Server header
server_tokens off;

# Disallow the browser to render the page inside an frame or iframe
add_header X-Frame-Options SAMEORIGIN;

# Disable content-type sniffing on some browsers.
add_header X-Content-Type-Options nosniff;

# Enables the Cross-site scripting (XSS) filter
add_header X-XSS-Protection \"1; mode=block\";

server {
    listen         80;
    server_name    "$2";
    return         301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl default deferred;
    server_name "$2";
    root /home/vagrant/groupeat/current/public;

    ssl_certificate /etc/nginx/ssl/nginx.crt;
    ssl_certificate_key /etc/nginx/ssl/nginx.key;

    # Enable session resumption to improve HTTPS performance
    ssl_session_cache shared:SSL:50m;
    ssl_session_timeout 5m;

    # Enables server-side protection from BEAST attacks
    ssl_prefer_server_ciphers on;

    # Disable SSLv3 since it's less secure then TLS
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2;

    # Enable HSTS (HTTP Strict Transport Security)
    add_header Strict-Transport-Security \"max-age=31536000; includeSubdomains;\";

    index index.html index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found on; }
    location = /robots.txt  { access_log off; log_not_found on; }

    access_log /var/log/nginx/access.log;
    error_log  /var/log/nginx/error.log error;

    sendfile off;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
"

echo "$block" > "/etc/nginx/sites-available/groupeat"
ln -fs "/etc/nginx/sites-available/groupeat" "/etc/nginx/sites-enabled/groupeat"
service nginx restart
service php5-fpm restart

echo "Creating groupeat PostgreSQL database"
sudo -u postgres psql -c "CREATE ROLE groupeat LOGIN UNENCRYPTED PASSWORD '$3' SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;"
sudo -u postgres /usr/bin/createdb --echo --owner=groupeat groupeat
service postgresql restart
su postgres -c "dropdb groupeat --if-exists"
su postgres -c "createdb -O groupeat groupeat"

if [[ $1 == 'local' ]]; then
    echo "Adding codecept alias"
    echo "alias codecept='~vagrant/groupeat/current/vendor/bin/codecept'" >> ~vagrant/.zshrc
fi

echo "Setting up ZSH to go directly to the app folder by default"
echo "cd ~vagrant/groupeat/current" >> ~vagrant/.zshrc
