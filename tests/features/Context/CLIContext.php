<?php
use Behat\Gherkin\Node\PyStringNode;

class CLIContext extends BehatAtoumContext
{
    private $output;
    private $status;

    /**
     * @Given /^I run \"(?P<command>[^\"]*)\"$/
     */
    public function iRun($command)
    {
        $this->output = null;
        $this->status = -1;

        $process = new \Symfony\Component\Process\Process($command);
        $process->run(function($type, $buffer) use(& $output) {
            $output .= $buffer;
        });

        $output = explode(PHP_EOL, $output);
        foreach ($output as & $line) {
            if ('' === trim($line)) {
               $line = trim($line);
            } else {
                $line = rtrim($line);
            }
        }

        $this->output = implode(PHP_EOL, $output);
        $this->status = $process->getExitCode();
    }

    /**
     * @Then /^I should see$/
     */
    public function iShouldSee(PyStringNode $string)
    {
        $actual = preg_replace("/\033\[[0-9]+;?[0-9]*m/", '', $this->output);
        $expected = (string) $string;

        $this->assert
            ->string($actual)
            ->isEqualTo(
                $expected,
                sprintf(
                    'Expected %s%sGot %s',
                    sprintf('string (%d)%s%s', strlen($expected), PHP_EOL, $expected),
                    PHP_EOL,
                    sprintf('string (%d)%s%s', strlen($actual), PHP_EOL, $actual)
                )
            )
        ;
    }

    /**
     * @Then /^I should see no output$/
     */
    public function iShouldSeeNoOutput()
    {
        $actual = preg_replace("/\033\[[0-9]+;?[0-9]*m/", '', $this->output);

        $this->assert
            ->string($this->output)
            ->isEmpty(
                sprintf(
                    'Expected empty output%sGot %s',
                    PHP_EOL,
                    sprintf('string (%d)%s%s', strlen($actual), PHP_EOL, $actual)
                )
            )
        ;
    }

    /**
     * @Then /^I should see output matching$/
     */
    public function iShouldSeeOutputMatching(PyStringNode $string)
    {
        $actual = preg_replace("/\033\[[0-9]+;?[0-9]*m/", '', $this->output);
        $expected = (string) $string;

        $this->assert
            ->string($actual)
            ->match(
                '/' . $expected . '/',
                sprintf(
                    'String %s%sDoes not match %s',
                    sprintf('string (%d)%s%s', strlen($actual), PHP_EOL, $actual),
                    PHP_EOL,
                    sprintf('string (%d)%s%s', strlen($expected), PHP_EOL, $expected)
                )
            )
        ;
    }

    /**
     * @Then /^The command should exit with success status$/
     */
    public function theCommandShouldExitWithSuccessStatus()
    {
        $this->assert
            ->integer($this->status)
            ->isEqualTo(
                0,
                sprintf('The command exited with a non-zero status code: int (%d)', $this->status)
            )
        ;
    }

    /**
     * @Then /^The command should exit with failure status$/
     */
    public function theCommandShouldExitWithFailureStatus()
    {
        $this->assert
            ->integer($this->status)
            ->isNotEqualTo(
                0,
                'The command exited with a zero status code'
            )
        ;
    }

    /**
     * @Then /^I am in "(?P<path>[^\"]*)"$/
     * @Then /^I go to "(?P<path>[^\"]*)"$/
     */
    public function iAmIn($path)
    {
        $this->assert
            ->boolean(chdir($path))
            ->isTrue(
                sprintf(
                    'Could not change directory to %s',
                    $path
                )
            )
        ;
    }
}
