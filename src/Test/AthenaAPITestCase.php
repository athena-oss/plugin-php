<?php
namespace Athena\Test;

use Athena\Athena;
use Athena\Event\Adapter\PhpUnitAdapter;
use Athena\Validator\TestAllowedValidator;
use PHPUnit_Framework_TestResult;

abstract class AthenaAPITestCase extends \PHPUnit_Framework_TestCase
{
    use TestAllowedValidator;

    /**
     * Runs the test case and collects the results in a TestResult object.
     * If no TestResult object is passed a new one will be created.
     *
     * @param  PHPUnit_Framework_TestResult $result
     * @return PHPUnit_Framework_TestResult
     * @throws \PHPUnit_Framework_Exception
     */
    public function run(PHPUnit_Framework_TestResult $result = null)
    {
        if ($result === null) {
            $result = $this->createResult();
        }


        if ($this->validateTestType('api')) {
            return parent::run($result);
        }

        return $result;
    }
}

