<?php

namespace MyExamples;

use \Example\Calc;

class InteractionExampleTest extends IntegrationExampleTestCase
{
    /**
     * @spec
     */
    public function test1()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        1 * $a->add(_,_);

        when:
        $a->add(1,2);

        then:
        notThrown();
    }

}


