<?php
namespace Tests\Api;

use Athena\Athena;
use Athena\Test\AthenaAPITestCase;

class DummyApiTest extends AthenaAPITestCase
{

    public function testApiSyntax()
    {
        $result = Athena::api()
                            ->get('http://httpbin.org/ip')
                            ->then()
                            ->assertThat()
                            ->responseIsJson()
                            ->statusCodeIs(200)
                            ->retrieve()
                            ->fromJson();
        $this->assertTrue(is_array($result));
    }
}
