Feature: php:switch
  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:switch on"
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status
      And I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:switch off"
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:switch php-6.6.6"
     Then I should see
        """



          [InvalidArgumentException]
          Version php-6.6.6 is not installed



        php:switch [-g|--global] [-a|--apache2] version
        php switch [-g|--global] [-a|--apache2] version



        """
      And The command should exit with failure status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And The PHP version "php-5.3.15" is installed
      And I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:switch php-5.3.15"
     Then I should see
        """
        PHP switched to php-5.3.15

        """
      And The command should exit with success status
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:switch off"
     Then I should see
        """
        PHP switched to system default version

        """
      And The command should exit with success status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "source ../prefix/.phpswitchrc && PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home php switch on"
     Then I should see output matching
        """
        Version phpswitch-5\.[0-9a-zA-Z\-\.]*.* is not installed
        """
      And The command should exit with failure status
    Given I run "source ../prefix/.phpswitchrc && PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home php switch off"
     Then I should see output matching
        """
        PHP switched to system default version
        PHP 5\.[0-9a-zA-Z\-\.]*.* \(cli\) \(built: .*\)
        """
      And The command should exit with success status
