Feature: Install
  Scenario: Install from sources in the current directory
    Given I run the installer "" command
     Then I should see output matching
        """
        >>> Installed phpswitch master@[a-z0-9]{7}
        >> Updating dependencies\.\.\.
        >>> File .*?\/phpswitch does not exist\.
        >>> ln -s .*?\/workspace\/bin\/phpswitch .*?\/phpswitch
        >> phpswitch sources path : .*?\/workspace
        >> phpswitch bin path : .*?\/workspace\/bin\/phpswitch \-> .*?\/phpswitch
        >> phpswitch was successfully installed\. Enjoy!
        """
    Given I run "../phpswitch"
     Then The command should exit with success status

  Scenario: Update existing installation
    Given I run the installer "" command
      And I run the installer "" command
     Then I should see output matching
        """
        >>> Updated phpswitch to master@[a-z0-9]{7}
        >> Updating dependencies\.\.\.
        >>> File .*?\/phpswitch exists\.
        >>> ln -s .*?\/workspace\/bin\/phpswitch .*?\/phpswitch
        >> phpswitch sources path : .*?\/workspace
        >> phpswitch bin path : .*?\/workspace\/bin\/phpswitch \-> .*?\/phpswitch
        >> phpswitch was successfully installed\. Enjoy!
        """
