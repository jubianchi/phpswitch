Feature: init
  Scenario:
    Given phpswitch is initialized
      And The PHP version "php-5.5.9" is installed and globally enabled
      And The PHP version "php-5.5.10" is installed
     When I run the "php:current" command
     Then I should see
        """
        php-5.5.9

        """

    Given The PHP version "php-5.5.10" is locally enabled
     When I run the "php:current" command
     Then I should see
        """
        php-5.5.10

        """

    Given The directory "sandbox/subdirectory" exists
      And I am in "sandbox/subdirectory"
     When I run the "php:current" command
     Then I should see
        """
        php-5.5.10

        """