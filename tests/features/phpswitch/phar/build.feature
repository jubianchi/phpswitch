Feature: phar:build
  Scenario:
    Given I run the "phar:build" command
      And The command should exit with success status
     Then The file "sandbox/phpswitch.phar" should exist

    Given I run "php phpswitch.phar"
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
         --ansi             Force ANSI output.
         --no-ansi          Disable ANSI output.
         --no-interaction -n Do not ask any interactive question.

        Available commands:
         help              Displays help for a command
         init              Initializes PhpSwitch environment
         list              Lists commands
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
      And The command should exit with success status

    Given I run "php phpswitch.phar init" without env
     Then I should see
        """
        Directory ./.phpswitch was created
        Directory ./.phpswitch/downloads was created
        Directory ./.phpswitch/sources was created
        Directory ./.phpswitch/installed was created
        Directory ./.phpswitch/doc was created
        You should source ./.phpswitch/.phpswitchrc to use phpswitch

        """
      And The directory "sandbox/.phpswitch" should exist
      And The command should exit with success status
      And The directory "sandbox/.phpswitch/downloads" should exist
      And The directory "sandbox/.phpswitch/sources" should exist
      And The directory "sandbox/.phpswitch/installed" should exist
      And The directory "sandbox/.phpswitch/doc" should exist
      And The file "sandbox/.phpswitch/.phpswitchrc" should exist

  Scenario:
    Given I run the "phar:build phpswitch-custom.phar" command
     Then The file "sandbox/phpswitch-custom.phar" should exist

    Given I run "php phpswitch-custom.phar"
      And The command should exit with success status

    Given I run "php phpswitch-custom.phar init" without env
      And The command should exit with success status
      And The directory "sandbox/.phpswitch" should exist
      And The directory "sandbox/.phpswitch/downloads" should exist
      And The directory "sandbox/.phpswitch/sources" should exist
      And The directory "sandbox/.phpswitch/installed" should exist
      And The directory "sandbox/.phpswitch/doc" should exist
      And The file "sandbox/.phpswitch/.phpswitchrc" should exist

  Scenario:
    Given I run the "phar:build" command
     Then The file "sandbox/phpswitch.phar" should exist

    Given I run "php phpswitch.phar"
      And The command should exit with success status

    Given I run "mkdir ../prefix && PHPSWITCH_PREFIX=../prefix php phpswitch.phar init"
      And The command should exit with success status
      And The directory "prefix/" should exist
      And The directory "prefix/downloads" should exist
      And The directory "prefix/sources" should exist
      And The directory "prefix/installed" should exist
      And The directory "prefix/doc" should exist
      And The file "prefix/.phpswitchrc" should exist
