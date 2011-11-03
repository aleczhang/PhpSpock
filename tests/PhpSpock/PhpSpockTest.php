<?php
/**
 * Date: 11/3/11
 * Time: 10:38 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock;

class PhpSpecTest extends \PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        parent::tearDown();

        \Mockery::close();
    }

    /**
     * @test
     */
    public function executeSpecification()
    {
        $phpSpock = new PhpSpock();

        $phpSpock->run(function()
            {
                setup:
                $a = 1;

                when:
                $a++;

                then:
                $a == 2;
            });
    }

    /**
     * @test
     * @expectedException PhpSpock\Specification\AssertionException
     */
    public function executeSpecificationWithBrokenTest()
    {
        $phpSpock = new PhpSpock();

        $phpSpock->run(function()
            {
                setup:
                $a = 1;

                when:
                $a++;

                then:
                $a == 3;
            });
    }

    /**
     * @test
     * @expectedException PhpSpock\Specification\AssertionException
     */
    public function executeTestFromCallback()
    {
        $phpSpock = new PhpSpock();
        $phpSpock->run(array($this, 'mySpec'));
    }

    public function mySpec()
    {
        setup:
        $a = 1;

        when:
        $a++;

        then:
        $a == 3;
    }
}
