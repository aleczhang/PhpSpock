<?php
/**
 * Date: 11/10/11
 * Time: 9:45 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace StackExample;

use \MyExamples\IntegrationExampleTestCase;
use \Example\Stack;

class StackWithThreeElementsTest extends IntegrationExampleTestCase {

    /**
     * @var \Example\Stack
     */
    public $stack;

    protected function setUp()
    {
        parent::setUp();

        $this->stack = new Stack();
        $this->stack->push("element");
        $this->stack->push("element2");
        $this->stack->push("element3");
    }

    /**
     * @spec
     */
    public function testSize()
    {
        expect:
        $this->stack->size() == 3;
    }

    /**
     * @spec
     */
    public function testPop()
    {
        expect:
        $this->stack->pop() == "element3";
        $this->stack->pop() == "element2";
        $this->stack->pop() == "element";
        $this->stack->size() == 0;
    }

    /**
     * @spec
     */
    public function testPeak()
    {
        expect:
        $this->stack->peek() == "element3";
        $this->stack->peek() == "element3";
        $this->stack->peek() == "element3";
        $this->stack->size() == 3;
    }

    /**
     * @spec
     */
    public function testPush()
    {
        when:
        $this->stack->push("element4");

        then:
        $this->stack->size() == 4;
        $this->stack->peek() == "element4";
    }


}
