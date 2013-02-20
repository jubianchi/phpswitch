Feature: Check
  Scenario: Default PHP config (no php.ini)
    Given I run "PHPSWITCH_PATH=/tmp PHPSWITCH_SYMLINK=/tmp php -n ./bin/installer --check"
     Then I should see output matching
        """
        > phpswitch installer
        >> Checking requirements...
        >>> Actual PHP version is 5\.[0-9a-zA-Z\-\.]*
        >>> cURL extension is enabled
        >>> You have required permissions on \/tmp
        """

  Scenario: Two differents target directories (no php.ini)
    Given I run "PHPSWITCH_PATH=/opt PHPSWITCH_SYMLINK=/bin php -n ./bin/installer --check"
     Then I should see output matching
        """
        > phpswitch installer
        >> Checking requirements...
        >>> Actual PHP version is 5\.[0-9a-zA-Z\-\.]*
        >>> cURL extension is enabled
        >>> You don't have required permissions on \/bin, \/opt
        """

  Scenario: Global install (no php.ini)
    Given I run "php -n ./bin/installer --check --global"
     Then I should see output matching
        """
        > phpswitch installer
        >> Checking requirements...
        >>> Actual PHP version is 5\.[0-9a-zA-Z\-\.]*
        >>> cURL extension is enabled
        >>> You don't have required permissions on \/usr\/local\/bin, \/usr\/share
        """

  Scenario: open_basedir restriction (no php.ini)
    Given I run "php -n -dopen_basedir=/tmp ./bin/installer --check --global"
     Then I should see output matching
        """
        > phpswitch installer
        >> Checking requirements\.\.\.
        >>> Actual PHP version is 5\.[0-9a-zA-Z\-\.]*
        >>> cURL extension is enabled
        >>> open_basedir restriction : \/tmp
        >>>> To fix this issue, try to run :
        >>>> PHPSWITCH_PATH=\/an\/allowed\/path .\/installer or curl https:\/\/raw\.github\.com\/jubianchi\/phpswitch\/master\/bin\/installer \| PHPSWITCH_PATH=\/an\/allowed\/path php
        >>>> or
        >>>> php \-dopen_basedir= \.\/installer or curl https:\/\/raw\.github\.com\/jubianchi\/phpswitch\/master\/bin\/installer \| php \-dopen_basedir=
        >>> You don't have required permissions on \/usr\/local\/bin, \/usr\/share
        """
