#!/usr/bin/env bash

cd ~vagrant/groupeat
php artisan down
git pull
composer install
php artisan optimize
php artisan up
