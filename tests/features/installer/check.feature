Feature: Check
  Scenario: Default PHP config (no php.ini)
    Given I run the installer "--check" command
     Then I should see output matching
        """
        > phpswitch installer
        >> Checking requirements...
        >>> Actual PHP version is 5\.[0-9a-zA-Z\-\.]*
        >>> cURL extension is (?:not )?enabled
        >>> PCNTL extension is (?:not )?enabled
        >>> readline extension is (?:not )?enabled
        >>> You have required permissions on .*?
        """

  Scenario: Two differents target directories (no php.ini)
    Given I run the installer "--check" command with env:
        """
        PHPSWITCH_PATH=/opt/phpswitch
        PHPSWITCH_SYMLINK=/bin
        """
     Then I should see output matching
        """
        > phpswitch installer
        >> Checking requirements...
        >>> Actual PHP version is 5\.[0-9a-zA-Z\-\.]*
        >>> cURL extension is (?:not )?enabled
        >>> PCNTL extension is (?:not )?enabled
        >>> readline extension is (?:not )?enabled
        >>> You don't have required permissions on \/bin, \/opt
        """

  Scenario: Global install (no php.ini)
    Given I run the installer "--check --global" command without env
     Then I should see output matching
        """
        > phpswitch installer
        >> Checking requirements...
        >>> Actual PHP version is 5\.[0-9a-zA-Z\-\.]*
        >>> cURL extension is (?:not )?enabled
        >>> PCNTL extension is (?:not )?enabled
        >>> readline extension is (?:not )?enabled
        >>> You don't have required permissions on \/usr\/local\/bin, \/usr\/share
        """

  Scenario: open_basedir restriction (no php.ini)
    Given I run the installer "--check --global" command with PHP options "-n -dopen_basedir=/tmp"
    Then I should see output matching
        """
        > phpswitch installer
        >> Checking requirements\.\.\.
        >>> Actual PHP version is 5\.[0-9a-zA-Z\-\.]*
        >>> cURL extension is (?:not )?enabled
        >>> PCNTL extension is (?:not )?enabled
        >>> readline extension is (?:not )?enabled
        >>> open_basedir restriction : \/tmp
        >>>> To fix this issue, try to run :
        >>>> PHPSWITCH_PATH=\/an\/allowed\/path .\/installer or curl https:\/\/raw\.github\.com\/jubianchi\/phpswitch\/master\/bin\/installer \| PHPSWITCH_PATH=\/an\/allowed\/path php
        >>>> or
        >>>> php \-dopen_basedir= \.\/installer or curl https:\/\/raw\.github\.com\/jubianchi\/phpswitch\/master\/bin\/installer \| php \-dopen_basedir=
        >>> You don't have required permissions on .*?
        """
