<?php

namespace MyExamples;

use \Example\Calc;

class ExceptionExampleTest extends IntegrationExampleTestCase
{

    /**
     * @spec
     * @expectedException Exception
     */
    public function testIndex()
    {
        when:
        throw new \Exception("test");

        then:
        $this->fail("Exception should be thrown!");
    }

    /**
     * @spec
     */
    public function testIndexWithNoExeptionThrown()
    {
        when:
        1==1;

        then:
        notThrown("RuntimeException");
    }

    /**
     * @spec
     */
    public function testIndexWithThrown()
    {
        when:
        throw new \Exception("test");

        then:
        thrown("Exception");
    }



    /**
     * @spec
     */
    public function testIndexWithThrown2()
    {
        when:
        throw new \RuntimeException("test");

        then:
        thrown("Exception");
    }

    /**
     * @spec
     */
    public function testIndexWithThrown3()
    {
        when:
        throw new \RuntimeException("test");

        then:
        thrown("RuntimeException");
    }

    /**
     * @spec
     */
    public function testIndexWithThrown4()
    {
        when:
        1==1;

        then:
        notThrown("RuntimeException");
    }

    /**
     * @spec
     */
    public function testExceptionCombination()
    {
        when:
        1==1;

        then:
        notThrown("RuntimeException");
        
        _when:
        throw new \RuntimeException("test");

        _then:
        thrown("RuntimeException");
    }
}


