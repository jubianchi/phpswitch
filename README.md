phpswitch
=========

A CLI utility to help you build and test PHP versions

## How to install

```shell
$ curl https://raw.github.com/jubianchi/phpswitch/master/bin/installer | sudo php
```

## How to use

```shell
# Install a new PHP version
$ phpswitch php:install php-5.4.8 --default --pdo --hash

# Switch to the newly installed PHP version
$ source /usr/share/phpswitch/.phpswitch/.phpswitchrc 
$ php switch php-5.4.8

$ php -v

# Restore the default PHP version
$ php switch off

$ php -v
```
