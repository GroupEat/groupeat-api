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

# read -p "This script should not be used on a running production server. Do you want to continue ? (y|n) " -n 1 -r
# echo
# if [[ ! $REPLY =~ ^[Yy]$ ]]
#   then
#   echo
#   echo "Aborting."
#   exit
# fi

#read -p "Please enter the production server IP: " ip
ip="178.62.158.190"
validateIP $ip

if [[ $? -ne 0 ]]; then
  echo "Invalid IP Address ($ip)"
  exit
fi

# read -p "Enter Shippable deployement key: " shippableKey
shippableKey="ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDAXklV4dV/s5V8lgyTLLfqF+sXGWUfhhey6jNBOiObeYrwM3lDilqkC6YIz36kkfxSzmMhROHaiXl+aHcTUPb3UVHr5iKiGuVuIuvTT3XNNzsoo6zcsidKUI1qWm0k4dwd/Jb27B1NflGZcD0QwLyHuN0r4KDg4woxB/NjUhAie/XhIAMi9Xi8x5uAekdp5aVtoBpu5M8GJbwW1vQ3fB6CaXDDlR5rrdY0oyiKcJEVLJuam4g70GIh8b67+gBrD+U4Zs1ntRXE8dW7DLs1vtCw2ECYm9UcBEe5G+rxE5XHN1HfigpNvEmEViPNhdfpfkz8tY1TFWgaddKkEXZKncVR 546a9689adedef14000bbd2d"

echo "Trying to ssh as root user into the production server..."
cd $( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/production
ssh root@"$ip" "bash -s" < ./init.sh "$shippableKey"

echo
echo "Fetching remote public rsa key"
echo
ssh root@"$ip" "cat ~vagrant/.ssh/id_rsa.pub"
echo
read -n1 -r -p "Add the previous SSH key into your GitHub account and press any key to continue..." uselessKey

# read -p "Choose application key: " appKey
appKey="LLYGfhqxgVLXcbilLfdCkd8xwotU9zOh"
# read -p "Choose password for the PostgreSQL DB: " postgresPassword
postgresPassword="Meyne_IS2012"
# read -p "Enter GitHub access token: " githubToken
githubToken="c5d31e9eba81ef687d44ff0126e48d38269a0c78"

ssh root@"$ip" "bash -s" < ./clone.sh "$appKey" "$postgresPassword" "$githubToken"
