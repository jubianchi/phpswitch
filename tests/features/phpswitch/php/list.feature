Feature: php:list
  Scenario:
    Given I run the "php:list" command
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given phpswitch is initialized
      And I run the "php:list" command
     Then I should see output matching
        """
         Available versions
           (?:(?:Fetching|Parsing) http:\/\/.*?)
        (?:php-(?:[0-9]\.?)*\s+http:\/\/.*)+
        """
      And The command should exit with success status
      And The file "workspace/phpswitch.cache" should exist

  Scenario:
    Given phpswitch is initialized
      And I run the "php:list --available" command
     Then I should see output matching
        """
        ^ Available versions
           (?:(?:Fetching|Parsing) http:\/\/.*?)
        (?:php-(?:[0-9]\.?)*\s+http:\/\/.*)+
        """
      And The command should exit with success status

  Scenario:
    Given phpswitch is initialized
      And I run the "php:list --installed" command
     Then I should see output matching
        """
        ^ Installed versions

        """
      And The command should exit with success status

  Scenario:
    Given phpswitch is initialized
      And I run "source ../workspace/.phpswitchrc && php list"
     Then I should see output matching
        """
        ^ Installed versions

        """
