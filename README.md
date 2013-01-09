# phpswitch [![Build Status](https://secure.travis-ci.org/jubianchi/phpswitch.png)](http://travis-ci.org/jubianchi/phpswitch)

A CLI utility to help you build and test PHP versions

Documentation : [http://jubianchi.github.com/phpswitch/](http://jubianchi.github.com/phpswitch/)

* [How to install](#how-to-install)
* [How to use](#how-to-use)
   * [Enable phpswitch](#enable-phpswitch)
   * [Install a new PHP version](#install-a-new-php-version)
   * [Switch PHP version](#switch-php-version)
   * [Display the current PHP version](#display-the-current-php-version)
   * [Manage PHP configuration](#manage-php-configuration)
   * [Working with Apache2](#working-with-apache2)
* [Extras](#extras)
   * [Offline documentation](#offline-documentation)
   * [Shell prompt](#shell-prompt)

## How to install

```shell
$ curl https://raw.github.com/jubianchi/phpswitch/master/bin/installer | sudo php

# Creates the phpswitch workspace
$ phpswitch init
```

This will install phpswitch in ```/usr/share/phpswitch``` and create the workspace in the installation directory
so make sure you have write access on this directory.

Read the [installer documentation](http://jubianchi.github.com/phpswitch/installer.html) to get more informations.

## How to use

* [Enable phpswitch](#enable-phpswitch)
* [Install a new PHP version](#install-a-new-php-version)
* [Switch PHP version](#switch-php-version)
* [Display the current PHP version](#display-the-current-php-version)
* [Manage PHP configuration](#manage-php-configuration)
* [Working with Apache2](#working-with-apache2)

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

**Once enabled, phpswitch will override the default ```php``` command. To get usage informations, use ```$ php -h```**

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

To switch PHP version use this command :

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

### Display the current PHP version

To display the current PHP version, use one of these commands :

```shell
# Will dislpay the PHP version message
$ php -v

# Will display the phpswitch PHP version name
$ phpswitch php:current

# Combine output of both previous commands
$ php current
```

### Get the list of available PHP version

To get the list of installed PHP versions, use this command :

```shell
$ php list
```

If you want to get the list of available (installable) PHP versions, use this command :

```shell
$ phpswitch php:list --available
```

### Manage PHP configuration

With phpswitch you will be able to quickly define or retrieve configuration directives :

**phpswitch is only able to manage configuration for PHP versions installed through it**

```shell
# Retrieves the value of date.timezone
$ phpswitch php:config date.timezone

# Defines the value of date.timezone
$ phpswitch php:config date.timezone Europe/Paris

# Same as previous commands
$ php config date.timezone
$ php config date.timezone Europe/Paris
```

**phpswitch is only able to work with configuration directives that where defined through it :**

```shell
$ phpswitch install 5.4.9 --default

# This command will fail as the directive was not defined through phpswitch
$ php config error_reporting

$ php config error_reporting E_ALL

# The command will now succeed as the value was previously defined through phpswitch
$ php config error_reporting
error_reporting => E_ALL
```

### Working with Apache2

phpswitch is able to trigger Apache2 modules build and manage them for you. You will only have to
ask when you want to switch the current module :

```shell
$ phpswitch php:install 5.4.8 --default --apxs2=/usr/sbin/apxs

$ php switch php-5.4.8 --apache2
Switching Apache2 module to php-5.4.8
You should restart apache2 using one of:
    - sudo /etc/init.d/apache2 restart
    - sudo service apache2 restart
    - sudo apachectl restart
    - ...
PHP switched to php-5.4.8
PHP 5.4.8 (cli) (built: Dec 31 2012 23:43:05)
Copyright (c) 1997-2012 The PHP Group
Zend Engine v2.4.0, Copyright (c) 1998-2012 Zend Technologies

$ php switch off                                                                                                          ⮂ Mar 1 jan 2013 01:39:15 ⮂ 72%
Restoring system default Apache2 module
You should restart apache2 using one of:
    - sudo /etc/init.d/apache2 restart
    - sudo service apache2 restart
    - sudo apachectl restart
    - ...
PHP switched to system default version
PHP 5.3.15 with Suhosin-Patch (cli) (built: Aug 24 2012 17:45:44)
Copyright (c) 1997-2012 The PHP Group
Zend Engine v2.3.0, Copyright (c) 1998-2012 Zend Technologies
    with Xdebug v2.2.1, Copyright (c) 2002-2012, by Derick Rethans
```

## Extras

* [Offline documentation](#offline-documentation)
* [Shell prompt](#shell-prompt)

### Offline documentation

phpswitch can help you install and manage your offline PHP documentations :

```
$ phpswitch help php:doc:install
Usage:
 php:doc:install [-f|--format[="..."]] [-l|--lang[="..."]]

Options:
 --format (-f)         Documentation format (html, single-html, chm, chm-enhanced) (default: "html")
 --lang (-l)           Documentation language (en, pt, cn, fr, de, it, ja, pl, ro, ru, es, tr) (default: "en")
```

This command will install the chosen documentation in the phpswitch workspace to allow offline browsing.
You can downland and install multiple formats and languages side by side.

### Shell prompt

If you are using [my ZSH theme](https://github.com/jubianchi/dotfiles), you may want to use the phpswitch prompt segment.
To do so, make your ```.zshrc``` match the following lines :

```shell
##
## ZSH Theme Configuration
##
source /usr/share/phpswitch/.phpswitch/.phpswitchprompt

LEFT_PROMPT=(status context dir phpswitch sf2 vagrant git); export LEFT_PROMPT
RIGHT_PROMPT=(date battery); export RIGHT_PROMPT
ZSH_THEME="jubianchi"
```

As you can see, you will need to source the ```.phpswitchprompt``` file and add the ```phpswitch```
segment to one of the two prompt parts.
