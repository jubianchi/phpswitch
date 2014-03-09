Feature: php:switch
  Scenario:
    Given I run the "php:switch on" command
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status
      And I run the "php:switch off" command
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given phpswitch is initialized
      And I run the "php:switch php-6.6.6" command
     Then I should see
        """



          [InvalidArgumentException]
          Version php-6.6.6 is not installed



        php:switch [-a|--apache2] [-s|--save] version
        php switch [-a|--apache2] [-s|--save] version



        """
      And The command should exit with failure status

  Scenario:
    Given phpswitch is initialized
      And The PHP version "php-5.3.15" is installed
      And I run the "php:switch php-5.3.15" command
     Then I should see
        """
        PHP switched to php-5.3.15

        """
      And The command should exit with success status
    Given I run the "php:switch off" command
     Then I should see
        """
        PHP switched to system default version

        """
      And The command should exit with success status

  Scenario:
    Given phpswitch is initialized
      And I have the following configuration in "sandbox/.phpswitch.yml":
        """
        phpswitch:
            version: phpswitch-5.3.21
        """
      And I run "source ../workspace/.phpswitchrc && php switch on"
     Then I should see output matching
        """
        Version phpswitch-5\.3\.21 is not installed
        """
      And The command should exit with failure status
    Given I run "source ../workspace/.phpswitchrc && php switch off"
     Then I should see output matching
        """
        PHP switched to system default version
        PHP 5\.[0-9a-zA-Z\-\.]*.* \(cli\) \(built: .*\)
        """
      And The command should exit with success status
