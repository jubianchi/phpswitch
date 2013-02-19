Feature: init
  Background:
    Given I run "test -d ./phpswitch && rm -rf ./phpswitch"
      And I run "test -f ./.phpswitch.yml && rm -f ./.phpswitch.yml"

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