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


    /**
     * @spec
     */
    public function test2()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $a->add(1,2);

        then:
        1 * $a->add(_,_);

        when_:
        $a->add(1,2);

        then_:
        1 * $a->add(_,_);

        when__:
        1==1;

        then__:
        1 * $a->add(_,_);
    }


    /**
     * @spec
     */
    public function test3()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(_,_) >> 4;
        $b == 4;
    }


    /**
     * @spec
     */
    public function test5()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(_,_) >> usingClosure(function($one, $two) { return $one + $two + 1; });
        $b == 4;
    }


    /**
     * @spec
     * @expectedException \RuntimeException
     * @expectedExceptionMessage foo
     */
    public function test6()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(_,_) >> throws(new \RuntimeException('foo'));
        $b == 4;
    }

    /**
     * @spec
     * @expectedException \RuntimeException
     * @expectedExceptionMessage foo
     */
    public function test7()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(_,_) >> throws('RuntimeException', 'foo');
        $b == 4;
    }


    /**
     * @spec
     */
    public function test8()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        1 * $a->add(_,_) >> throws(new \RuntimeException('foo'));

        when:
        $b = $a->add(1,2);

        then:
        thrown();
    }

    /**
     * @spec
     */
    public function test9()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        1 * $a->add(_,_) >> throws('RuntimeException', 'foo');

        when:
        $b = $a->add(1,2);

        then:
        thrown('RuntimeException');
    }

}


