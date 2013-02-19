Feature: php:current
  Background:
    Given I run "test -d ./phpswitch && rm -rf ./phpswitch"
      And I run "test -f ./.phpswitch.yml && rm -f ./.phpswitch.yml"

  Scenario:
    Given I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch php:current"
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch init"
      And I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch php:current"
     Then I should see no output
      And The command should exit with success status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch init"
      And I have the following configuration in ".phpswitch.yml":
        """
        phpswitch:
            version: 5.3.15
        """
      And I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch php:current"
     Then I should see
        """
        5.3.15
        """
      And The command should exit with success status