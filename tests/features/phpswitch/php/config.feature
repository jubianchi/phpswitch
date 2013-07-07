Feature: php:current
  Scenario:
    Given I run the "php:config foo.bar" command
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given I run the "init" command
      And The PHP version "php-5.3.15" is installed and enabled
      And I run the "php:config foo.bar" command
     Then I should see
        """



          [InvalidArgumentException]
          Configuration directive foo.bar is not managed by phpswitch



        php:config name [value]



        """
      And The command should exit with failure status
