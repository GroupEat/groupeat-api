[![Build Status](https://api.shippable.com/projects/54a71363d46935d5fbc15ac1/badge?branchName=master)](https://app.shippable.com/projects/54a71363d46935d5fbc15ac1/builds/latest)

# Git commands

Instead of the usual `git pull` and `git push` commands, use the `php artisan pull` and `php artisan push` commands to interact with the Git repository as it will execute additional needed tasks automatically. However, before running `php artisan pull` for the first time (i.e. after cloning this repo) you need to run `composer install` to install the required Composer dependencies. If something goes wrong it could mean that your Vagrant box is not up to date: please refer to the troubleshooting section below.

# Testing the code

Codeception is used to test the application. Just after pulling from git you should run the `./vendor/bin/codecept build` command to generate the needed testing classes. Then use `./vendor/bin/codecept run` to execute the whole test suite.
