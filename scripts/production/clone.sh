#!/usr/bin/env bash

appKey="$1"
postgresPassword="$2"
githubToken="$3"

echo "$postgresPassword"
echo "$githubToken"

echo "Installing git"
apt-get install -y git

echo "Switching to vagrant user"
su vagrant
cd ~vagrant

echo "Adding GitHub to the known hosts"
echo -e "Host github.com\n\tStrictHostKeyChecking no\n" >> ~/.ssh/config

echo "Cloning the repository"
git clone git@github.com:GroupEat/groupeat-web.git

#if [ -d ~vagrant/groupeat ]; then
  echo "Groupeat directory already exists. Aborting..."
#  exit
#else
  echo "Moving the repository to groupeat directory"
#  mv groupeat-web groupeat

  echo "Starting provisionning as root"
  sudo -s ~vagrant/groupeat/scripts/provision.sh "$postgresPassword"

  echo "Creating and editing the .env.production.php file"
  cd ~vagrant/groupeat
echo "<?php

return [
  'APP_KEY' => '$appKey',
  'PGSQL_PASSWORD' => '$postgresPassword',
];
"
echo "<?php

return [
  'APP_KEY' => '$appKey',
  'PGSQL_PASSWORD' => '$postgresPassword',
];
  " > .env.production.php

  echo "Installing Composer dependencies"
  sudo composer config -g github-oauth.github.com "$githubToken"
  composer install
#fi
