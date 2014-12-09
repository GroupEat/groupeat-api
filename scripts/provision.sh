#!/usr/bin/env bash

echo "Installing some PPAs"
apt-get install -y software-properties-common
apt-add-repository ppa:nginx/stable -y
apt-add-repository ppa:ondrej/php5-5.6 -y

echo "Updating packages list"
apt-get update
apt-get upgrade -y

echo "Installing git"
apt-get install -y git

echo "Installing some basic packages"
apt-get install -y build-essential curl dos2unix gcc libmcrypt4 libpcre3-dev make re2c supervisor whois vim

echo "Setting timezone"
ln -sf /usr/share/zoneinfo/CET /etc/localtime

echo "Installing PHP stuffs"
apt-get install -y php5-cli php5-dev php-pear php5-pgsql \
php5-apcu php5-json php5-curl php5-gd php5-gmp php5-imap \
php5-mcrypt php5-xdebug php5-memcached

echo "Making MCrypt available"
ln -s /etc/php5/conf.d/mcrypt.ini /etc/php5/mods-available
sudo php5enmod mcrypt

echo "Ipasswd -l nstalling Mailparse PECL extension"
pecl install mailparse
echo "extension=mailparse.so" > /etc/php5/mods-available/mailparse.ini
ln -s /etc/php5/mods-available/mailparse.ini /etc/php5/cli/conf.d/20-mailparse.ini

echo "Installing Composer"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
/usr/local/bin/composer self-update

echo "Adding Composer global bin to path"
printf "\nPATH=\"~vagrant/.composer/vendor/bin:\$PATH\"\n" | tee -a ~vagrant/.profile

echo "Setting some PHP CLI settings"
sudo sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php5/cli/php.ini
sudo sed -i "s/display_errors = .*/display_errors = On/" /etc/php5/cli/php.ini
sudo sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php5/cli/php.ini
sudo sed -i "s/;date.timezone.*/date.timezone = CET/" /etc/php5/cli/php.ini

echo "Installing Nginx & PHP-FPM"
apt-get install -y nginx php5-fpm
rm /etc/nginx/sites-enabled/default
rm /etc/nginx/sites-available/default
service nginx restart

echo "Setting up some PHP-FPM options"
ln -s /etc/php5/mods-available/mailparse.ini /etc/php5/fpm/conf.d/20-mailparse.ini
sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php5/fpm/php.ini
sed -i "s/display_errors = .*/display_errors = On/" /etc/php5/fpm/php.ini
sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php5/fpm/php.ini
sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php5/fpm/php.ini
sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php5/fpm/php.ini
echo "xdebug.remote_enable = 1" >> /etc/php5/fpm/conf.d/20-xdebug.ini
echo "xdebug.remote_connect_back = 1" >> /etc/php5/fpm/conf.d/20-xdebug.ini
echo "xdebug.remote_port = 9000" >> /etc/php5/fpm/conf.d/20-xdebug.ini

echo "Copying fastcgi_params to Nginx because they broke it on the PPA"
cat > /etc/nginx/fastcgi_params << EOF
fastcgi_param	QUERY_STRING		\$query_string;
fastcgi_param	REQUEST_METHOD		\$request_method;
fastcgi_param	CONTENT_TYPE		\$content_type;
fastcgi_param	CONTENT_LENGTH		\$content_length;
fastcgi_param	SCRIPT_FILENAME		\$request_filename;
fastcgi_param	SCRIPT_NAME		\$fastcgi_script_name;
fastcgi_param	REQUEST_URI		\$request_uri;
fastcgi_param	DOCUMENT_URI		\$document_uri;
fastcgi_param	DOCUMENT_ROOT		\$document_root;
fastcgi_param	SERVER_PROTOCOL		\$server_protocol;
fastcgi_param	GATEWAY_INTERFACE	CGI/1.1;
fastcgi_param	SERVER_SOFTWARE		nginx/\$nginx_version;
fastcgi_param	REMOTE_ADDR		\$remote_addr;
fastcgi_param	REMOTE_PORT		\$remote_port;
fastcgi_param	SERVER_ADDR		\$server_addr;
fastcgi_param	SERVER_PORT		\$server_port;
fastcgi_param	SERVER_NAME		\$server_name;
fastcgi_param	HTTPS			\$https if_not_empty;
fastcgi_param	REDIRECT_STATUS		200;
EOF

echo "Setting the Nginx & PHP-FPM user"
sed -i "s/user www-data;/user vagrant;/" /etc/nginx/nginx.conf
sed -i "s/# server_names_hash_bucket_size.*/server_names_hash_bucket_size 64;/" /etc/nginx/nginx.conf
sed -i "s/user = www-data/user = vagrant/" /etc/php5/fpm/pool.d/www.conf
sed -i "s/group = www-data/group = vagrant/" /etc/php5/fpm/pool.d/www.conf
sed -i "s/;listen\.owner.*/listen.owner = vagrant/" /etc/php5/fpm/pool.d/www.conf
sed -i "s/;listen\.group.*/listen.group = vagrant/" /etc/php5/fpm/pool.d/www.conf
sed -i "s/;listen\.mode.*/listen.mode = 0666/" /etc/php5/fpm/pool.d/www.conf

echo "Configuring Nginx"
block="server {
  listen 80;
  server_name groupeat.fr;
  root /home/vagrant/groupeat/public;
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

echo "Adding vagrant user to www-Data"
usermod -a -G www-data vagrant
id vagrant
groups vagrant

echo "Installing PostgreSQL"
apt-get install -y postgresql-9.3 postgresql-contrib

echo "Configuring PostgreSQL remote access"
sed -i "s/#listen_addresses = 'localhost'/listen_addresses = '*'/g" /etc/postgresql/9.3/main/postgresql.conf
echo "host    all             all             10.0.2.2/32               md5" | tee -a /etc/postgresql/9.3/main/pg_hba.conf
sudo -u postgres psql -c "CREATE ROLE groupeat LOGIN UNENCRYPTED PASSWORD '$1' SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;"
sudo -u postgres /usr/bin/createdb --echo --owner=groupeat groupeat
service postgresql restart

echo "Creating the database"
su postgres -c "dropdb groupeat --if-exists"
su postgres -c "createdb -O groupeat groupeat"

echo "Adding ZSH shell"
apt-get install -y zsh
if [ ! -d ~vagrant/.oh-my-zsh ]; then
  git clone https://github.com/robbyrussell/oh-my-zsh.git ~vagrant/.oh-my-zsh
fi

cp ~vagrant/.oh-my-zsh/templates/zshrc.zsh-template ~vagrant/.zshrc
chown vagrant: ~vagrant/.zshrc
sed -i -e 's/ZSH_THEME=".*"/ZSH_THEME="ys"/' ~vagrant/.zshrc
chsh -s /bin/zsh vagrant

cp -r ~vagrant/.oh-my-zsh /root/.oh-my-zsh
cp ~vagrant/.zshrc /root/.zshrc
chown root: /root/.zshrc
sed -i -e 's/ZSH_THEME=".*"/ZSH_THEME="ys"/' /root/.zshrc
chsh -s /bin/zsh root

ysTheme='function box_name {
  [ -f .box-name ] && cat .box-name || hostname
}

# Directory info.
local current_dir='\''${PWD/#$HOME/~}'\''

# Git info.
local git_info='\''$(git_prompt_info)'\''
ZSH_THEME_GIT_PROMPT_PREFIX=" %{$fg[white]%}on%{$reset_color%} git:%{$fg[cyan]%}"
ZSH_THEME_GIT_PROMPT_SUFFIX="%{$reset_color%}"
ZSH_THEME_GIT_PROMPT_DIRTY=" %{$fg[red]%}x"
ZSH_THEME_GIT_PROMPT_CLEAN=" %{$fg[green]%}o"

# Prompt format: \n # USER at MACHINE in DIRECTORY on git:BRANCH STATE \n $
PROMPT="
%{$terminfo[bold]$fg[blue]%}#%{$reset_color%} \
%{$fg[cyan]%}%n \
%{$fg[white]%}at \
%{$fg[green]%}$(box_name) \
%{$fg[white]%}in \
%{$terminfo[bold]$fg[yellow]%}${current_dir}%{$reset_color%}\
${git_info} \
%{$fg[white]%}
%{$terminfo[bold]$fg[red]%}$ %{$reset_color%}"'
echo "$ysTheme" > ~vagrant/.oh-my-zsh/themes/ys.zsh-theme
echo "$ysTheme" > /root/.oh-my-zsh/themes/ys.zsh-theme

echo "Going directly to the web folder by default"
echo "cd ~vagrant/groupeat" >> ~vagrant/.zshrc

echo "Adding some useful aliases"
echo "alias ..='cd ..'" >> ~vagrant/.zshrc
echo "alias ...='cd ../..'" >> ~vagrant/.zshrc
echo "alias h='cd ~'" >> ~vagrant/.zshrc
echo "alias c='clear'" >> ~vagrant/.zshrc
echo "alias art='php artisan'" >> ~vagrant/.zshrc
echo "alias cri='composer install'" >> ~vagrant/.zshrc
echo "alias cru='composer update'" >> ~vagrant/.zshrc
echo "alias crd='composer dump-autoload'" >> ~vagrant/.zshrc
echo "alias phpunit='~vagrant/groupeat/vendor/bin/phpunit'" >> ~vagrant/.zshrc
