#!/usr/bin/env bash

# Update packages list
apt-get update
apt-get upgrade -y

# Install some PPAs
apt-get install -y software-properties-common
apt-add-repository ppa:nginx/stable -y
apt-add-repository ppa:rwky/redis -y
apt-add-repository ppa:chris-lea/node.js -y
apt-add-repository ppa:ondrej/php5-5.6 -y

# Update packages list
apt-get update

# Install git
apt-get install -y git

# Install some basic packages
apt-get install -y build-essential curl dos2unix gcc libmcrypt4 libpcre3-dev \
make python2.7-dev python-pip re2c supervisor unattended-upgrades whois vim

# Set timezone
ln -sf /usr/share/zoneinfo/CET /etc/localtime

# Install PHP stuffs
apt-get install -y php5-cli php5-dev php-pear \
php5-mysqlnd php5-pgsql php5-sqlite \
php5-apcu php5-json php5-curl php5-gd \
php5-gmp php5-imap php5-mcrypt php5-xdebug \
php5-memcached php5-redis

# Make MCrypt available
ln -s /etc/php5/conf.d/mcrypt.ini /etc/php5/mods-available
sudo php5enmod mcrypt

# Install Mailparse PECL extension
pecl install mailparse
echo "extension=mailparse.so" > /etc/php5/mods-available/mailparse.ini
ln -s /etc/php5/mods-available/mailparse.ini /etc/php5/cli/conf.d/20-mailparse.ini

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
/usr/local/bin/composer self-update

# Add Composer global bin to path
printf "\nPATH=\"~vagrant/.composer/vendor/bin:\$PATH\"\n" | tee -a ~vagrant/.profile

# Set some PHP CLI settings
sudo sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php5/cli/php.ini
sudo sed -i "s/display_errors = .*/display_errors = On/" /etc/php5/cli/php.ini
sudo sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php5/cli/php.ini
sudo sed -i "s/;date.timezone.*/date.timezone = CET/" /etc/php5/cli/php.ini

# Install Nginx & PHP-FPM
apt-get install -y nginx php5-fpm
rm /etc/nginx/sites-enabled/default
rm /etc/nginx/sites-available/default
service nginx restart

# Add the HHVM key & repository
wget -O - http://dl.hhvm.com/conf/hhvm.gpg.key | apt-key add -
echo deb http://dl.hhvm.com/ubuntu trusty main | tee /etc/apt/sources.list.d/hhvm.list
apt-get update
apt-get install -y hhvm

# Configure HHVM to run as vagrant
service hhvm stop
sed -i 's/#RUN_AS_USER="www-data"/RUN_AS_USER="vagrant"/' /etc/default/hhvm
service hhvm start

# Start HHVM on system start
update-rc.d hhvm defaults

# Setup some PHP-FPM options
ln -s /etc/php5/mods-available/mailparse.ini /etc/php5/fpm/conf.d/20-mailparse.ini
sed -i "s/error_reporting = .*/error_reporting = E_ALL/" /etc/php5/fpm/php.ini
sed -i "s/display_errors = .*/display_errors = On/" /etc/php5/fpm/php.ini
sed -i "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /etc/php5/fpm/php.ini
sed -i "s/memory_limit = .*/memory_limit = 512M/" /etc/php5/fpm/php.ini
sed -i "s/;date.timezone.*/date.timezone = UTC/" /etc/php5/fpm/php.ini
echo "xdebug.remote_enable = 1" >> /etc/php5/fpm/conf.d/20-xdebug.ini
echo "xdebug.remote_connect_back = 1" >> /etc/php5/fpm/conf.d/20-xdebug.ini
echo "xdebug.remote_port = 9000" >> /etc/php5/fpm/conf.d/20-xdebug.ini

# Copy fastcgi_params to Nginx because they broke it on the PPA
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

# Set the Nginx & PHP-FPM user
sed -i "s/user www-data;/user vagrant;/" /etc/nginx/nginx.conf
sed -i "s/# server_names_hash_bucket_size.*/server_names_hash_bucket_size 64;/" /etc/nginx/nginx.conf
sed -i "s/user = www-data/user = vagrant/" /etc/php5/fpm/pool.d/www.conf
sed -i "s/group = www-data/group = vagrant/" /etc/php5/fpm/pool.d/www.conf
sed -i "s/;listen\.owner.*/listen.owner = vagrant/" /etc/php5/fpm/pool.d/www.conf
sed -i "s/;listen\.group.*/listen.group = vagrant/" /etc/php5/fpm/pool.d/www.conf
sed -i "s/;listen\.mode.*/listen.mode = 0666/" /etc/php5/fpm/pool.d/www.conf
service nginx restart
service php5-fpm restart

# Add vagrant user to www-Data
usermod -a -G www-data vagrant
id vagrant
groups vagrant

# Install Node
apt-get install -y nodejs
npm install -g grunt-cli
npm install -g gulp
npm install -g bower

# Install Postgres
apt-get install -y postgresql postgresql-contrib

# Configure Postgres remote access
sed -i "s/#listen_addresses = 'localhost'/listen_addresses = '*'/g" /etc/postgresql/9.3/main/postgresql.conf
echo "host    all             all             10.0.2.2/32               md5" | tee -a /etc/postgresql/9.3/main/pg_hba.conf
sudo -u postgres psql -c "CREATE ROLE groupeat LOGIN UNENCRYPTED PASSWORD 'secret' SUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;"
sudo -u postgres /usr/bin/createdb --echo --owner=groupeat groupeat
service postgresql restart

# Create database
su postgres -c "dropdb groupeat --if-exists"
su postgres -c "createdb -O groupeat groupeat"

# Install a few other things
apt-get install -y redis-server memcached beanstalkd

# Configure beanstalkd
sudo sed -i "s/#START=yes/START=yes/" /etc/default/beanstalkd
sudo /etc/init.d/beanstalkd start

# Add ZSH shell
apt-get install -y zsh
if [ ! -d ~vagrant/.oh-my-zsh ]; then
  git clone https://github.com/robbyrussell/oh-my-zsh.git ~vagrant/.oh-my-zsh
fi
cp ~vagrant/.oh-my-zsh/templates/zshrc.zsh-template ~vagrant/.zshrc
chown vagrant: ~vagrant/.zshrc
sed -i -e 's/ZSH_THEME=".*"/ZSH_THEME="ys"/' ~vagrant/.zshrc
echo "source ~vagrant/groupeat/scripts/aliases.sh" >> ~vagrant/.zshrc
chsh -s /bin/zsh vagrant

# Go directly to the web folder by default
echo "cd ~vagrant/groupeat" >> ~vagrant/.zshrc
