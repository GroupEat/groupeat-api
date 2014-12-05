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
 - Browse to http://groupeat.dev and make sure it works.

# Usage

## SSH session

Run the `vagrant ssh` command to SSH into the freshly created virtual machine.
Remember that some useful aliases are specified at the end of `server/provision.sh`, so don't hesitate to use them!

## Connect to PostgreSQL

Download [Valentina Studio](http://www.valentina-db.com/en/all-downloads) if you don't already have an equivalent solution and
use the following information:
 - method: TCP/IP
 - host: 127.0.0.1
 - port: 54320
 - user: groupeat
 - password: groupeat
 - database: groupeat
