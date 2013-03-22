Feature: php:current
  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:current"
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:current"
     Then I should see no output
      And The command should exit with failure status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And The PHP version "php-5.3.15" is installed and enabled
      And I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:current"
     Then I should see
        """
        php-5.3.15

        """
      And The command should exit with success status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "source ../prefix/.phpswitchrc && php current"
     Then I should see output matching
        """
        PHP 5\.[0-9a-zA-Z\-\.]*.* \(cli\) \(built: .*\)
        """
      And The command should exit with success status
