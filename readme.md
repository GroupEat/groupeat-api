[![Build Status](https://api.shippable.com/projects/54a71363d46935d5fbc15ac1/badge?branchName=master)](https://app.shippable.com/projects/54a71363d46935d5fbc15ac1/builds/latest)

# Installation

## Introduction

This project use Vagrant in order not to mess with your computer setup. Everything will be installed and run in a virtual machine, leaving your actual environment untouched! The added benefit is that the local development environment will be exactly the same than the production one

## Common part

Clone this repository on your machine. Place it where you like and rename it if you want but make sure you won't change your mind because moving it after the following steps will break things... Then `cd` into the project root so that you are in the folder of the `Vagrantfile`.

## Mac OS X
 - Run the `./scripts/mac_install.sh` command, sit back and you're done! Just stay around at the beginning of the process because your password may be asked a few times.

## Other
 - Download and install [VirtualBox](https://www.virtualbox.org/wiki/Downloads) and [Vagrant](https://www.vagrantup.com/downloads.html).
 - Fill the missing data if any in `example.env.php` and copy this file to `.env.local.php` and `.env.testing.php`.
 - Add the line `192.168.10.10  groupeat.dev` to your hosts file:
   - Windows: `c:\windows\systeme32\drivers\etc`
   - Unix: `/etc/hosts`
 - Run the `vagrant up` command and wait for it to finish (a few minutes depending on your internet connection).
 - Browse to https://groupeat.dev and make sure it works.

# Local Usage

## Uncertified certificate

The SSL certificate used in local development is self-signed in order to be free. You can safely ignore your browser warnings about potential security issues and accept the certificate.

## Access to the VM

As soon as the VM is up you should SSH into it with the `vagrant ssh` command if you want to tinker with the application. In fact, you should not run commands outside of the VM because you may not have everything installed properly on your host machine.
Remember that some useful aliases are specified at the end of `server/provision.sh`, so don't hesitate to use them!

## Git commands

Instead of the usual `git pull` and `git push` commands, use the `art pull` and `art push` commands to interact with the Git repository as it will execute additional needed tasks automatically.

## Administration zone

This zone is always available on local environment but on the production server you will have to fill a form to be granted access. You should be able to use the admin@groupeat.fr account with the usual GroupEat password to log in.

Some useful admin routes are defined to tinker with the application :

 - https://groupeat.dev/docs: Read the API documentation (use the `?generate=true` query string to regenerate it you made some change to the docs file)
 - https://groupeat.dev/db: PostgreSQL management (ignore the eventual error message and click on 'Login')
 - https://groupeat.dev/logs: View the application logs (from both Nginx and CLI)
 - https://groupeat.dev/phpinfo: Open the PHPinfo page
