# phpswitch [![Build Status](https://secure.travis-ci.org/jubianchi/phpswitch.png)](http://travis-ci.org/jubianchi/phpswitch)

A CLI utility to help you build and test PHP versions

## How to install

```shell
$ curl https://raw.github.com/jubianchi/phpswitch/master/bin/installer | sudo php
```

## How to use

### Enable phpswitch

You can temporarily enable phpswitch using this command :

```shell
source /usr/share/phpswitch/.phpswitch/.phpswitchrc
```

If you want to permanently enable phpswitch, add these lines to your ```.bashrc``` or ```.zshrc``` :

```shell
source /usr/share/phpswitch/.phpswitch/.phpswitchrc
php switch on > /dev/null
```

### Install a new PHP version

To install a new PHP version using the default options, run this command :

```shell
$ phpswitch php:install 5.4.8 --default
```

To get the full options list, use this command :

```shell
$ phpswitch help php:install
```

With phpswitch, you can install the same PHP version many times using different configurations :

```shell
$ phpswitch php:install 5.4.8 --alias=atoum --atoum
```

This will install a new php-5.4.8 with the default atoum configuration. The name of this environment will
be ```atoum-5.4.8```.

### Switch PHP version

When enabled, phpswitch will override the default ```php``` command. You will then be able to switch version using :

```shell
$ php switch php-5.4.8
```

If you want to switch to an aliased PHP version, use this command :

```shell
$ php switch atoum-5.4.8
```

To restore the system default PHP version, use this command :

```shell
$ php switch off
```
