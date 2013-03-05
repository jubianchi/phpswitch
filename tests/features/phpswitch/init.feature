Feature: init
  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
     Then I should see
        """
        Directory ../prefix was created
        Directory ../prefix/downloads was created
        Directory ../prefix/sources was created
        Directory ../prefix/installed was created
        Directory ../prefix/doc was created
        You should source ../prefix/.phpswitchrc to use phpswitch

        """
  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And I run "source ../prefix/.phpswitchrc && PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home php -h"
     Then I should see output matching
        """
        phpswitch commands:
          - php list                   Lists available PHP versions
          - php current                Displays current PHP version
          - php config <key> \[<value>\] Gets or Sets configuration for current PHP version
          - php repl                   Enters PHP REPL
          - php switch <version>       Switches current PHP version
        """
