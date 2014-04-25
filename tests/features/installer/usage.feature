Feature: Usage
  Scenario:
    Given I run the installer "--usage" command
     Then I should see
        """
        > phpswitch installer
        --global        : Install phpswitch as a global command
        --check         : Only run system requirements check

        Environment :
        PHPSWITCH_GIT_URL : Git repository (default : git://github.com/jubianchi/phpswitch.git)
        PHPSWITCH_GIT_BRANCH : Git branch (default : master)
        PHPSWITCH_PATH  : Installation directory (default : /usr/share/phpswitch)
        PHPSWITCH_SYMLINK : phpswitch bin symlink path (default: /usr/local/bin)

        Examples :
        $ curl https://raw.github.com/jubianchi/phpswitch/master/bin/installer | sudo php -- --global
        $ curl https://raw.github.com/jubianchi/phpswitch/master/bin/installer | PHPSWITCH_PATH=/home/me php

        """
