#!/usr/bin/env bash

block="server {
  listen 80;
  server_name groupeat.dev;
  root /home/vagrant/groupeat/public;
  index index.html index.htm index.php;
  charset utf-8;
  location / {
    try_files \$uri \$uri/ /index.php?\$query_string;
  }
  location = /favicon.ico { access_log off; log_not_found off; }
  location = /robots.txt  { access_log off; log_not_found off; }
  access_log off;
  error_log  /var/log/nginx/groupeat.dev-error.log error;
  error_page 404 /index.php;
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

echo "$block" > "/etc/nginx/sites-available/groupeat.dev"
ln -fs "/etc/nginx/sites-available/groupeat.dev" "/etc/nginx/sites-enabled/groupeat.dev"
service nginx restart
service php5-fpm restart
