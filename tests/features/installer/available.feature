Feature: Available
  Scenario: Installer URL
    Given I run "wget -O /tmp/installer https://raw.github.com/jubianchi/phpswitch/master/bin/installer > /dev/null 2>&1"
     Then The command should exit with success status
    Given I run "rm /tmp/installer > /dev/null 2>&1"
     Then The command should exit with success status
