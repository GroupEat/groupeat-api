#!/usr/bin/env bash

if [ ! -f Vagrantfile ]; then
  echo "Please call this script from the Vagrantfile folder (ie project root)."
  exit
else
  echo "Vagrant file found"
fi

echo 'Copying the environment variable file'
cp example.env.php .env.local.php

while grep -q FILL_ME .env.local.php; do
  echo 'Please fill the missing data in .env.example.php'
  read -n1 -r -p "Press any key to continue " key
  echo ''
done

if grep -Fxq "192.168.10.10  groupeat.dev" /etc/hosts; then
  echo "/etc/hosts file already modified"
else
  echo "Adding '192.168.10.10  groupeat.dev' to /etc/hosts"
  echo "192.168.10.10  groupeat.dev" | sudo tee -a /etc/hosts
fi

which -s brew
if [[ $? != 0 ]]; then
  echo "Installing Homebrew"
  ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
else
  echo "Updating Homebrew"
  brew update -v
fi

if [[ -n $(brew ls --versions brew-cask) ]]; then
  echo 'Brew-cask already installed'
else
  echo "Installing Brew-cask"
  brew install caskroom/cask/brew-cask
fi

if [[ -n $(brew ls --versions virtualbox) ]]; then
  echo 'Virtualbox already installed'
else
  echo "Installing Virtualbox"
  brew cask install virtualbox
fi

if [[ -n $(brew ls --versions vagrant) ]]; then
  echo 'Vagrant already installed'
else
  echo "Installing Vagrant"
  brew cask install vagrant
fi

echo 'Deleting old running VM'
vagrant destroy -f

echo 'Booting up the VM'
vagrant up

echo 'Opening the app in the default browser to check if everything works'
open "http://groupeat.dev"
