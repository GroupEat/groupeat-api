#!/usr/bin/env bash

echo "Successfully logged into production"
echo "Now initializing..."

echo "Setting up correct locale"
#sudo apt-get install -y language-pack-fr
#export LANG=fr_FR.UTF-8

echo "Adding the vagrant user"
adduser --disabled-password --gecos ""  vagrant

echo "Adding vagrant to the admin group"
echo %vagrant ALL=NOPASSWD:ALL > /etc/sudoers.d/vagrant
chmod 0440 /etc/sudoers.d/vagrant
usermod -a -G sudo vagrant

echo "Switching to vagrant user"
su vagrant

if [ ! -f ~vagrant/.ssh/id_rsa ]; then
  echo "Creating RSA keys"
  mkdir ~vagrant/.ssh

  ssh-keygen -t rsa -N "$1" -C "tib.dex@gmail.com" -f ~vagrant/.ssh/id_rsa
fi
