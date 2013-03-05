Feature: Usage
  Scenario:
    Given I run "../../bin/phpswitch"
     Then I should see
        """
        phpswitch version 0.1

        Usage:
          [options] command [arguments]

        Options:
          --help           -h Display this help message.
          --quiet          -q Do not output any message.
          --verbose        -v Increase verbosity of messages.
          --version        -V Display this application version.
          --ansi              Force ANSI output.
          --no-ansi           Disable ANSI output.
          --no-interaction -n Do not ask any interactive question.

        Available commands:
          help              Displays help for a command
          init              Initializes PhpSwitch environment
          list              Lists commands
        phar
          phar:build        Builds phpswitch Phar
          phar:extract      Builds phpswitch Phar
        php
          php:config        Get or set configuration
          php:current       Displays current PHP version
          php:doc:install   Installs PHP offline documentation
          php:install       Installs a PHP version
          php:list          Lists PHP versions
          php:repl          Enters PHP REPL
          php:switch        Switch PHP version
          php:uninstall     Uninstalls a PHP version

        """

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "source ../prefix/.phpswitchrc && PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home php -h"
     Then I should see output matching
        """
        phpswitch commands:
          \- php list                   Lists available PHP versions
          \- php current                Displays current PHP version
          \- php config <key> \[<value>\] Gets or Sets configuration for current PHP version
          \- php repl                   Enters PHP REPL
          \- php switch <version>       Switches current PHP version
        """
