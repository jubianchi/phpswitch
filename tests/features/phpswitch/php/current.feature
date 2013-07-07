Feature: php:current
  Scenario:
    Given I run the "php:current" command
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given I run the "init" command
      And I run the "php:current" command
     Then I should see no output
      And The command should exit with failure status

  Scenario:
    Given I run the "init" command
      And The PHP version "php-5.3.15" is installed and enabled
      And I run the "php:current" command
     Then I should see
        """
        php-5.3.15

        """
      And The command should exit with success status

  Scenario:
    Given I run the "init" command
      And I run "source ../workspace/.phpswitchrc && php current"
     Then I should see output matching
        """
        PHP 5\.[0-9a-zA-Z\-\.]*.* \(cli\) \(built: .*\)
        """
      And The command should exit with success status
