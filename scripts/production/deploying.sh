#!/usr/bin/env bash

cd ~vagrant/groupeat
art down
git pull
composer install
art optimize
art up
