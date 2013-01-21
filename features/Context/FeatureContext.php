<?php
use
    Behat\Behat\Context\BehatContext,
    Behat\Gherkin\Node\PyStringNode
;

class FeatureContext extends BehatContext {
    private $output;
    private $status;

    /**
     * @Given /^I run \"(?P<command>[^\"]*)\"$/
     */
    public function iRun($command)
    {
        $this->output = null;
        $this->status = null;
        exec($command, $this->output, $this->status);

        $this->output = implode(PHP_EOL, (array)$this->output);
    }

    /**
     * @Then /^I should see$/
     */
    public function iShouldSee(PyStringNode $string)
    {
        $actual = preg_replace("/\033\[[0-9]+;?[0-9]*m/", '', $this->output);
        $expected = trim((string)$string);

        if($actual !== $expected) {
            $expected = '(' . strlen($expected) . ')' . PHP_EOL . $expected .PHP_EOL . PHP_EOL;
            $actual = '(' . strlen($actual) . ')' . PHP_EOL . $actual . PHP_EOL . PHP_EOL;

            throw new \Exception(sprintf('Expected %sGot %s', $expected, $actual));
        }
    }

    /**
     * @Then /^I should see output matching$/
     */
    public function iShouldSeeOutputMatching(PyStringNode $string)
    {
        $actual = preg_replace("/\033\[[0-9]+;?[0-9]*m/", '', $this->output);
        $expected = trim((string)$string);
        $matches = array();
        if(false == preg_match_all('/' . $expected . '/', $actual, $matches)) {
            $expected = PHP_EOL . $expected .PHP_EOL . PHP_EOL;
            $actual = '(' . strlen($actual) . ')' . PHP_EOL . $actual . PHP_EOL . PHP_EOL;

            throw new \Exception(sprintf('String %sDoes not match %s', $actual, $expected));
        }
    }

    /**
     * @Then /^The command should exit with success status$/
     */
    public function theCommandShouldExitWithSuccessStatus()
    {
        if(0 !== $this->status) {
            throw new \Exception('The command exited with a non-zero status code (%d)', $this->status);
        }
    }
}