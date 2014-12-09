#!/usr/bin/env bash

appKey="$1"
postgresPassword="$2"
githubToken="$3"

echo "Installing git"
apt-get install -y git

echo "Adding GitHub to the known hosts"
echo -e "Host github.com\n\tStrictHostKeyChecking no\n" >> ~vagrant/.ssh/config
chown -R vagrant: ~vagrant/.ssh

echo "Cloning the repository"
cd ~vagrant
git clone git@github.com:GroupEat/groupeat-web.git

if [ -d ~vagrant/groupeat ]; then
  echo "Groupeat directory already exists. Aborting..."
  exit
else
  echo "Moving the repository to groupeat directory"
  mv groupeat-web groupeat

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

  echo "Installing Composer dependencies"
  composer config -g github-oauth.github.com "$githubToken"
  composer install

  echo "Changing to correct permissions"
  chown -R vagrant: ~vagrant
fi
