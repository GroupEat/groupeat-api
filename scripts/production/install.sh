#!/usr/bin/env bash

appKey="$1"
postgresPassword="$2"
githubToken="$3"

echo "Installing git"
apt-get install -y git

echo "Adding GitHub to the known hosts"
sudo -u vagrant echo -e "Host github.com\n\tStrictHostKeyChecking no\n" >> ~vagrant/.ssh/config

echo "Cloning the repository"
cd ~vagrant
sudo -u vagrant -H git clone -v git@github.com:GroupEat/groupeat-web.git

if [ -d ~vagrant/groupeat ]; then
echo "Groupeat directory already exists. Aborting..."
exit
else
echo "Moving the repository to groupeat directory"
mv ~vagrant/groupeat-web ~vagrant/groupeat

echo "Creating and editing the .env.production.php file"
cd ~vagrant/groupeat
echo "<?php

return [
  'APP_KEY' => '$appKey',
  'PGSQL_PASSWORD' => '$postgresPassword',
];
" > .env.production.php

echo "Starting provisionning as root"
~vagrant/groupeat/scripts/provision.sh "$postgresPassword"

echo "Configuring Nginx"
block="server {
  listen 80;
  server_name groupeat.fr;
  root /home/vagrant/groupeat/current/public;
  index index.html index.htm index.php;
  charset utf-8;
  location / {
    try_files \$uri \$uri/ /index.php?\$query_string;
  }
  location = /favicon.ico { access_log off; log_not_found off; }
  location = /robots.txt  { access_log off; log_not_found off; }
  access_log off;
  error_log  /var/log/nginx/groupeat.fr-error.log error;
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

echo "$block" > "/etc/nginx/sites-available/groupeat.fr"
ln -fs "/etc/nginx/sites-available/groupeat.fr" "/etc/nginx/sites-enabled/groupeat.fr"
service nginx restart
service php5-fpm restart

echo "Installing Composer dependencies"
composer config -g github-oauth.github.com "$githubToken"
composer install

echo "Changing to correct permissions"
chown -R vagrant: ~vagrant
fi
