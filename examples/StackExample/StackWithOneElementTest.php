<?php
/**
 * Date: 11/10/11
 * Time: 9:45 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace StackExample;

use \MyExamples\IntegrationExampleTestCase;
use \Example\Stack;

class StackWithOneElementTest extends IntegrationExampleTestCase {

    /**
     * @var \Example\Stack
     */
    public $stack;

    protected function setUp()
    {
        parent::setUp();

        $this->stack = new Stack();
        $this->stack->push("element");
    }

    /**
     * @spec
     */
    public function testSize()
    {
        expect:
        $this->stack->size() == 1;
    }

    /**
     * @spec
     */
    public function testPop()
    {
        when:
        $x = $this->stack->pop();

        then:
        $x == "element";
        $this->stack->size() == 0;
    }

    /**
     * @spec
     */
    public function testPeek()
    {
        when:
        $x =$this->stack->peek();

        then:
        $x == "element";
        $this->stack->size() == 1;
    }

    /**
     * @spec
     */
    public function testPush()
    {
        when:
        $this->stack->push("element2");

        then:
        $this->stack->size() == 2;
        $this->stack->peek() == "element2";
    }


}
