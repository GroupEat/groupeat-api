#!/usr/bin/env bash

cat /home/shippable/.ssh/id_rsa
cat /home/shippable/.ssh/id_rsa.pub
ssh -v vagrant@178.62.158.190 "bash -s" < $( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/production/deploying.sh
