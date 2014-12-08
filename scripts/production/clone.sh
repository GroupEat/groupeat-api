#!/usr/bin/env bash

echo "Installing git"
apt-get install -y git

echo "Switching to vagrant user"
su vagrant
cd ~vagrant

echo "Cloning the repository"
git clone git@github.com:GroupEat/groupeat-web.git

if [ -d ./groupeat ]; then
  echo "groupeat directory already exists. Aborting..."
  exit
else
  echo "Moving the repository to groupeat directory"
  mv groupeat-web groupeat

  echo "Switching back to root user"
  sudo su

  echo "Starting provisionning"
  ~vagrant/groupeat/scripts/provision.sh
fi
