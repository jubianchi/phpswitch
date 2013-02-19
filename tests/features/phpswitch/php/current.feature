Feature: php:current
  Background:
    Given I run "test -d ./phpswitch && rm -rf ./phpswitch"

  Scenario:
    Given I run "php -n ./bin/installer"
      And I run "./phpswitch/bin/phpswitch php:current"
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """