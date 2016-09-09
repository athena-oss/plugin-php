<?php
namespace Athena\Tests\Unit;

use Athena\Test\AthenaUnitTestCase;

class DummyUnitTest extends AthenaUnitTestCase
{
    public function testUnitSyntax()
    {
        $this->assertInstanceOf(AthenaUnitTestCase::class, $this);
    }
}