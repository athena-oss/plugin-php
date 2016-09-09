<?php
/**
 * Created by PhpStorm.
 * User: pproenca
 * Date: 14/01/16
 * Time: 16:06
 */

namespace Athena\Tests\Info;


use Athena\Info\AthenaInfo;

class AthenaInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInfo_OneTitleAndMessageIsAdd_ShouldPrintStringWithGivenTitleAndMessage()
    {
        $athenaInfo = new AthenaInfo();
        $athenaInfo->addLine('title', 'message');

        ob_start();
        $athenaInfo->printInfo();
        $printString = ob_get_clean();

        $expectedString = <<<EOF

- title : message

---------------------------------------

EOF;

        $this->assertEquals($expectedString, $printString);
    }
}