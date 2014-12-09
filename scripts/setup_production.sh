#!/usr/bin/env bash

function validateIP()
{
  local ip=$1
  local stat=1
  if [[ $ip =~ ^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$ ]]; then
    OIFS=$IFS
    IFS='.'
    ip=($ip)
    IFS=$OIFS
    [[ ${ip[0]} -le 255 && ${ip[1]} -le 255 \
    && ${ip[2]} -le 255 && ${ip[3]} -le 255 ]]
    stat=$?
  fi
  return $stat
}

read -p "This script should not be used on a running production server. Do you want to continue ? (y|n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]
  then
  echo
  echo "Aborting."
  exit
fi

read -p "Please enter the production server IP: " ip

validateIP $ip

if [[ $? -ne 0 ]]; then
  echo "Invalid IP Address ($ip)"
  exit
fi

read -p "Choose passphrase for the SSH Key: " sshPassphrase
echo

echo "Trying to ssh as root user into the production server..."
cd $( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/production
ssh root@"$ip" "bash -s" < ./init.sh "$sshPassphrase"

echo
ssh root@"$ip" "cat ~vagrant/.ssh/id_rsa.pub"
echo
read -n1 -r -p "Add the previous SSH key into your GitHub account and press any key to continue..." uselessKey

read -p "Choose application key: " appKey
read -p "Choose password for the PostgreSQL DB: " postgresPassword
read -p "Enter GitHub access token: " githubToken

ssh root@"$ip" "bash -s" < ./clone.sh "$appKey" "$postgresPassword" "$githubToken"
