[![Build Status](https://api.shippable.com/projects/54a71363d46935d5fbc15ac1/badge?branchName=master)](https://app.shippable.com/projects/54a71363d46935d5fbc15ac1/builds/latest)

# Installing

`git pull; composer install; php artisan pull`

# Updating

`php artisan pull`

# Linting

`./vendor/bin/phpcs --colors -p`

# Testing

`./vendor/bin/codecept run`

Before running this command, if you have not already done if since the last update, run `./vendor/bin/codecept build`.
