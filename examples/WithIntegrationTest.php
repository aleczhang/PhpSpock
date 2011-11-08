<?php

namespace MyExamples;

use \PhpSpock\Adapter\PhpUnitAdapter as PhpSpock;

class WithIntegrationTest extends IntegrationExampleTestCase
{
   /**
     * @spec
     */
    public function testIndex()
    {
        setup:
        $calc = new \Example\Calc();

        when:
        $b = $calc->add(1, 3);

        then:
        $b == 4;
    }
}


