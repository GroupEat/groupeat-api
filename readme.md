# Local Installation
 - Download and install [VirtualBox](https://www.virtualbox.org/wiki/Downloads) and [Vagrant](https://www.vagrantup.com/downloads.html).
 - Clone this repository on your machine. Place it where you want and make sure you won't change your mind because moving it after the next step will break things...
 - `cd` into the project root, run the `vagrant up` command and wait for it to finish (a few minutes depending on your internet connection).
 - Run `cp example.env.php .env.local.php` and fill the missing data (if any) in `.env.local.php`.
 - Browse to http://groupeat.app and make sure it works.

# Usage

## Connect to PostgreSQL

Download [Valentina Studio](http://www.valentina-db.com/en/all-downloads) if you don't already haven't an equivalent solution and
use the following information:
 - method: TCP/IP
 - host: 127.0.0.1
 - port: 54320
 - user: groupeat
 - password: secret
 - database: groupeat
