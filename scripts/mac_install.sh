#!/usr/bin/env bash

echo "Cd into project root"
cd $( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/..

while grep -qs FILL_ME .env.local.php; do
  echo 'Please fill the missing data in .env.example.php'
  read -n1 -r -p "Press any key to continue " key
  echo ''
done

if [ ! -f ./.env.local.php ]; then
    echo 'Copying example.env.php to .env.local.php'
    cp example.env.php .env.local.php
fi

if [ ! -f ./.env.testing.php ]; then
    echo 'Copying example.env.php to .env.testing.php'
    cp example.env.php .env.testing.php
fi

if grep -Fxqs "192.168.10.10  groupeat.dev" /etc/hosts; then
  echo "/etc/hosts file already modified"
else
  echo "Adding '192.168.10.10  groupeat.dev' to /etc/hosts"
  echo "192.168.10.10  groupeat.dev" | sudo tee -a /etc/hosts
fi

which -s brew
if [[ $? != 0 ]]; then
  echo "Installing Brew"
  ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
else
  echo "Updating Brew"
  brew update
fi

if [[ -n $(brew ls --versions brew-cask) ]]; then
  echo 'Updating Brew-cask'
  brew upgrade brew-cask
else
  echo "Installing Brew-cask"
  brew install caskroom/cask/brew-cask
fi

brew cask list virtualbox
if [[ $? != 0 ]]; then
    echo "Installing Virtualbox"
    brew cask install --force virtualbox
else
    echo "Updating Virtualbox"
    brew cask update virtualbox
fi

brew cask list vagrant
if [[ $? != 0 ]]; then
    echo "Installing Vagrant"
    brew cask install --force vagrant
else
    echo "Updating Vagrant"
    brew cask update vagrant
fi

echo "Cleanup Brew"
brew cleanup

echo 'Deleting old running VM'
vagrant destroy -f

echo 'Booting up the VM'
vagrant up

echo 'Opening the app in the default browser to check if everything works'
open "https://groupeat.dev"

echo "SSH into the VM"
vagrant ssh
