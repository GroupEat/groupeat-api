#!/usr/bin/env bash

appKey="$1"
postgresPassword="$2"

echo "Installing git"
apt-get install -y git

echo "Switching to vagrant user"
su vagrant
cd ~vagrant

echo "Adding GitHub to the known hosts"
echo -e "Host github.com\n\tStrictHostKeyChecking no\n" >> ~/.ssh/config

echo "Cloning the repository"
git clone git@github.com:GroupEat/groupeat-web.git

if [ -d ./groupeat ]; then
  echo "groupeat directory already exists. Aborting..."
  exit
else
  echo "Moving the repository to groupeat directory"
  mv groupeat-web groupeat

  echo "Starting provisionning as root"
  sudo ~vagrant/groupeat/scripts/provision.sh "$postgresPassword"

  echo "Creating and editing the .env.production.php file"
  cd ~vagrant/groupeat
  cp example.env.php .env.production.php
  sed -i "s/APP_KEY.*=>.*'.*'/APP_KEY' => '$appKey'/" .env.production.php
  sed -i "s/PGSQL_PASSWORD.*=>.*'.*'/PGSQL_PASSWORD' => '$postgresPassword'/" .env.production.php

  echo "Installing Composer dependencies"
  composer install
fi
