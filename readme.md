[![Circle CI](https://circleci.com/gh/GroupEat/groupeat-api.svg?style=shield&circle-token=5bccad853ce36f8ed516994d3abc07ac2fc7ecbd)](https://circleci.com/gh/GroupEat/groupeat-api)

# Installing

 - `cp example.env env`
 - Fill the missing data if any in `.env`
 - `composer install; php artisan db:install -s`

# Updating

`php artisan pull`

# Linting

`./vendor/bin/phpcs --colors -p`

# Testing

`./vendor/bin/codecept run`

Before running this command, if you have not already done if since the last update, run `./vendor/bin/codecept build`.
