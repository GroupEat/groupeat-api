#!/usr/bin/env bash

cd ~vagrant/groupeat
php artisan down
git pull
composer install --no-dev
php artisan optimize
php artisan up
