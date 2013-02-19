Feature: init
  Background:
    Given I run "test -d ./phpswitch && rm -rf ./phpswitch"

  Scenario:
    Given I run "php -n ./bin/installer"
      And I run "./phpswitch/bin/phpswitch init"
     Then I should see output matching
        """
        Directory [^\s]*phpswitch\/\.phpswitch was created
        Directory [^\s]*phpswitch\/\.phpswitch\/downloads was created
        Directory [^\s]*phpswitch\/\.phpswitch\/sources was created
        Directory [^\s]*phpswitch\/\.phpswitch\/installed was created
        Directory [^\s]*phpswitch\/\.phpswitch\/doc was created
        You should source [^\s]*phpswitch\/\.phpswitch\/\.phpswitchrc to use phpswitch
        """