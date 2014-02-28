Feature: Install and use
  Scenario:
    Given I run the "init" command
     Then The command should exit with success status

    Given I run the "php:install 5.4.0" command
     Then The command should exit with success status
      And I should see output matching
        """
        Installing PHP 5\.4\.0
        From mirror fr2\.php\.net
        Configure options: \[\]

        Downloading PHP 5\.4\.0
            http:\/\/.*?php\.net\/.*?php\-5\.4\.0\.tar\.bz2(?:\/from\/fr2.php.net\/mirror)?


        Extracting 5\.4\.0
            .*?\/workspace\/downloads\/php\-5\.4\.0\.tar\.bz2


        Building php\-5\.4\.0
            .*?\/workspace\/sources\/php\-5\.4\.0
            .*?\/workspace\/installed\/php\-5\.4\.0


        PHP version php\-5\.4\.0 was installed:
            .*?\/workspace\/installed\/php\-5\.4\.0

        Use php switch php-5\.4\.0 to enable it
        """
      And The PHP version "php-5.4.0" should be installed

    Given I run the "php:install 5.4.0" command
     Then The command should exit with failure status
      And I should see
        """
        PHP version php-5.4.0 is already installed
        """

    Given I run "source ../workspace/.phpswitchrc && php switch php-5.4.0 && php -v"
     Then The command should exit with success status
      And I should see output matching
        """
        PHP 5\.4\.0.*? \(cli\) \(built: .*\)
        """