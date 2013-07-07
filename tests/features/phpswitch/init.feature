Feature: init
  Scenario:
    Given I run the "init" command
     Then I should see output matching
        """
        Directory .*\/workspace was created
        Directory .*\/workspace\/downloads was created
        Directory .*\/workspace\/sources was created
        Directory .*\/workspace\/installed was created
        Directory .*\/workspace\/doc was created
        You should source .*\/workspace\/\.phpswitchrc to use phpswitch

        """
     And The directory "workspace" should exist
     And The directory "workspace/downloads" should exist
     And The directory "workspace/sources" should exist
     And The directory "workspace/installed" should exist
     And The directory "workspace/doc" should exist
     And The file "workspace/.phpswitchrc" should exist
  Scenario:
    Given I run the "init" command
      And I run "source ../workspace/.phpswitchrc && php -h"
     Then I should see output matching
        """
        phpswitch commands:
          - php list                   Lists available PHP versions
          - php current                Displays current PHP version
          - php config <key> \[<value>\] Gets or Sets configuration for current PHP version
          - php repl                   Enters PHP REPL
          - php switch <version>       Switches current PHP version
        """
