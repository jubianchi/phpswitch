Feature: php:current
  Background:
    Given I run "test -d ./phpswitch && rm -rf ./phpswitch"
      And I run "test -f ./.phpswitch.yml && rm -f ./.phpswitch.yml"

  Scenario:
    Given I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch php:config foo.bar"
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch init"
      And I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch php:config foo.bar"
     Then I should see
        """


          [InvalidArgumentException]
          Configuration directive foo.bar is not managed by phpswitch


        php:config name [value]


        """
      And The command should exit with failure status
