#!/usr/bin/env bash

# Add a line to the hosts file
if grep -Fxq "192.168.10.10  groupeat.dev" /etc/hosts
  then
  echo "Hosts file already modified"
else
  echo "Going to append 192.168.10.10  groupeat.dev to /etc/hosts file"
  echo "192.168.10.10  groupeat.dev" | sudo tee -a /etc/hosts
fi

# Install Homebrew
ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"

# Installing Brew Cask
brew install caskroom/cask/brew-cask

# Installing Virtualbox and Vagrant
brew cask install virtualbox
brew cask install vagrant

# Create the virtual machine
vagrant up

# Copy the environment variable file
cp example.env.php .env.local.php

# Open the default browser to check if everything works
open "http://groupeat.dev"
