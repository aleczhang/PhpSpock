<?php

namespace MyExamples;

use \Example\Calc;

class FixtureExampleTest extends IntegrationExampleTestCase
{
    /**
     * @var \Example\Calc
     */
    public $myResource;

    protected function setUp()
    {
        parent::setUp();

        $this->myResource = new Calc();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->myResource = null;
    }


    /**
     * @group debug
     * @spec
     */
    public function testIndex()
    {
        when:
        $b = $this->myResource->add(1, 3);

        then:
//        1 * $a->getCode() >> 1;
        $b == 4;
    }
}


