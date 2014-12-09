#!/usr/bin/env bash

appKey="$1"
postgresPassword="$2"
githubToken="$3"

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
cd ~vagrant/groupeat
git pull

#if [ -d ~vagrant/groupeat ]; then
  echo "Groupeat directory already exists. Aborting..."
#  exit
#else
  echo "Moving the repository to groupeat directory"
  #mv groupeat-web groupeat

  echo "Creating and editing the .env.production.php file"
  cd ~vagrant/groupeat
  echo "<?php

  return [
  'APP_KEY' => '$appKey',
  'PGSQL_PASSWORD' => '$postgresPassword',
  ];
  " > .env.production.php

  echo "Starting provisionning as root"
  echo "$postgresPassword" > /tmp/.psqlPassword
  sudo ~vagrant/groupeat/scripts/provision.sh
  rm -f /tmp/.psqlPassword

  echo "Installing Composer dependencies"
  cd ~vagrant/groupeat
  composer self-update
  "$githubToken"
  sudo chown -R vagrant: ~vagrant/.composer
  "$githubToken"
  composer config -g github-oauth.github.com "$githubToken"
  "$githubToken"
  composer install
#fi
