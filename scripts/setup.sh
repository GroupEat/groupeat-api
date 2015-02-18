#!/usr/bin/env bash

echo "Creating self-signed SSL certificate for HTTPS access"
mkdir /etc/nginx/ssl
openssl req -new -newkey rsa:2048 -days 365 -nodes -x509 \
    -subj "/C=FR/ST=Essonne/L=Palaiseau/O=GroupEat/CN=groupeat.dev" \
    -keyout /etc/nginx/ssl/nginx.key -out /etc/nginx/ssl/nginx.crt

NGINX_CONFIG="server {
    listen         80;
    server_name    groupeat.dev;
    return         301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl default deferred;
    server_name groupeat.dev;
    root /home/vagrant/groupeat/current/public;

    ssl_certificate /etc/nginx/ssl/nginx.crt;
    ssl_certificate_key /etc/nginx/ssl/nginx.key;

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

echo "${NGINX_CONFIG}" > "/etc/nginx/sites-available/groupeat"
ln -fs "/etc/nginx/sites-available/groupeat" "/etc/nginx/sites-enabled/groupeat"
service nginx restart
service php5-fpm restart

echo "Creating groupeat PostgreSQL database"
sudo -u postgres psql -c "CREATE ROLE groupeat LOGIN UNENCRYPTED PASSWORD 'groupeat' SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;"
sudo -u postgres /usr/bin/createdb --echo --owner=groupeat groupeat
service postgresql restart
su postgres -c "dropdb groupeat --if-exists"
su postgres -c "createdb -O groupeat groupeat"

echo "Adding codecept alias"
echo "alias codecept='~vagrant/groupeat/current/vendor/bin/codecept'" >> ~vagrant/.zshrc

echo "Setting up ZSH to go directly to the app folder by default"
echo "cd ~vagrant/groupeat/current" >> ~vagrant/.zshrc
