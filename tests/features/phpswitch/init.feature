Feature: init
  Scenario:
    Given I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch init"
     Then I should see output matching
        """
        Directory [^\s]*\/phpswitch was created
        Directory [^\s]*\/phpswitch\/downloads was created
        Directory [^\s]*\/phpswitch\/sources was created
        Directory [^\s]*\/phpswitch\/installed was created
        Directory [^\s]*\/phpswitch\/doc was created
        You should source [^\s]*\/phpswitch\/\.phpswitchrc to use phpswitch
        """
  Scenario:
    Given I run "PHPSWITCH_PREFIX=./phpswitch bin/phpswitch init"
      And I run "source ./phpswitch/.phpswitchrc && PHPSWITCH_PREFIX=./phpswitch php -h"
     Then I should see output matching
        """
        phpswitch commands:
          - php list                   Lists available PHP versions
          - php current                Displays current PHP version
          - php config <key> \[<value>\] Gets or Sets configuration for current PHP version
          - php switch <version>       Switches current PHP version
        """
