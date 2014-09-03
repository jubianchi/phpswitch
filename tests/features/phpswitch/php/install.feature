Feature: Install and use

  @not-travis
  Scenario:
    Given phpswitch is initialized
      And I run the "php:install 5.5.9" command
     Then The command should exit with success status
      And I should see output matching
        """
        Installing PHP 5\.5\.9
        From mirror fr2\.php\.net
        Configure options: \[\]

        Downloading PHP 5\.5\.9
            http:\/\/.*?php\.net\/.*?php\-5\.5\.9\.tar\.bz2(?:\/from\/fr2.php.net\/mirror)?


        Extracting 5\.5\.9
            .*?\/workspace\/downloads\/php\-5\.5\.9\.tar\.bz2


        Building php\-5\.5\.9
            .*?\/workspace\/sources\/php\-5\.5\.9
            .*?\/workspace\/installed\/php\-5\.5\.9


        PHP version php\-5\.5\.9 was installed:
            .*?\/workspace\/installed\/php\-5\.5\.9

        Use php switch php-5\.5\.9 to enable it
        """
      And The PHP version "php-5.5.9" should be installed

    Given I run the "php:install 5.5.9" command
     Then The command should exit with failure status
      And I should see
        """
        PHP version php-5.5.9 is already installed
        """

    Given I run "source ../workspace/.phpswitchrc && php switch php-5.5.9 && php -v"
     Then The command should exit with success status
      And I should see output matching
        """
        PHP 5\.5\.9.*? \(cli\) \(built: .*\)
        """