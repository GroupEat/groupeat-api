#!/usr/bin/env bash

TARGET=$1
BRANCH=$2

if [[ $(git log -1 HEAD --pretty=format:%s) != *skip\ deploy* ]]; then
  echo "Downloading Rocketeer"
  wget http://rocketeer.autopergamene.eu/versions/rocketeer.phar -O rocketeer.phar
  chmod +x rocketeer.phar

  echo "Deploying"
  ./rocketeer.phar deploy --on=$TARGET --branch=$BRANCH

  echo "Removing Rocketeer executable"
  rm -f rocketeer.phar
else
  echo "Deployment cancelled"
fi
