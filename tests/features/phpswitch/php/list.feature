Feature: php:list
  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:list"
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:list"
     Then I should see output matching
        """
         Installed versions
         Available versions
        (?:php-(?:[0-9]\.?)*\s+http:\/\/.*\n)+
        """
      And The command should exit with success status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:list --available"
     Then I should see output matching
        """
        ^ Available versions
        (?:php-(?:[0-9]\.?)*\s+http:\/\/.*\n)+
        """
      And The command should exit with success status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:list --installed"
     Then I should see output matching
        """
        ^ Installed versions

        """
      And The command should exit with success status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "source ../prefix/.phpswitchrc && PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home php list"
     Then I should see output matching
        """
        ^ Installed versions

        """
