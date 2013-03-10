Feature: php:current
  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:config foo.bar"
     Then I should see output matching
        """
        phpswitch is not initialized. Please run init command
        """
      And The command should exit with failure status

  Scenario:
    Given I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch init"
      And The PHP version "php-5.3.15" is installed and enabled
      And I run "PHPSWITCH_PREFIX=../prefix PHPSWITCH_HOME=../home ../../bin/phpswitch php:config foo.bar"
     Then I should see
        """



          [InvalidArgumentException]
          Configuration directive foo.bar is not managed by phpswitch



        php:config name [value]



        """
      And The command should exit with failure status
