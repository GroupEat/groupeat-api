#!/usr/bin/env bash

echo "Cd into project root"
cd $( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/..

exit;

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
  echo "Installing Homebrew"
  ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
fi

if [[ -n $(brew ls --versions brew-cask) ]]; then
  echo 'Updating Brew-cask'
  brew upgrade brew-cask
else
  echo "Installing Brew-cask"
  brew install caskroom/cask/brew-cask
fi

if [[ -n $(brew ls --versions virtualbox) ]]; then
  echo 'Updating Virtualbox'
  brew upgrade virtualbox
else
  echo "Installing Virtualbox "
  brew cask install --force virtualbox
fi

if [[ -n $(brew ls --versions vagrant) ]]; then
  echo 'Updating Vagrant'
  brew upgrade vagrant
else
  echo "Installing Vagrant"
  brew cask install --force vagrant
fi

if [[ -n $(brew ls --versions terminal-notifier) ]]; then
  echo 'Updating Terminal notifier'
  brew upgrade terminal-notifier
else
  echo "Installing Terminal notifier "
  brew cask install --force terminal-notifier
fi

vagrant plugin list | grep 'vagrant-notify' &> /dev/null
if [ $? == 0 ]; then
    echo "Vagrant notify plugin already installed"
else
    echo "Installing Vagrant notify plugin"
    vagrant plugin install vagrant-notify
fi

block="#!/usr/bin/env

bash/usr/local/bin/terminal-notifier -message \"\$5\" -title \"\$4\" -appIcon \"\$2\"
"

echo "Creating the notify-send file and givint it execution permission"
sudo echo "$block" > /usr/local/bin/notify-send
sudo chmod +x /usr/local/bin/notify-send

echo 'Deleting old running VM'
vagrant destroy -f

echo 'Booting up the VM'
vagrant up

echo 'Opening the app in the default browser to check if everything works'
open "https://groupeat.dev"

echo "SSH into the VM"
vagrant ssh
