<?php
/**
 * Date: 11/10/11
 * Time: 9:45 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace StackExample;

use \MyExamples\IntegrationExampleTestCase;
use \Example\Stack;

class EmptyStackTest extends IntegrationExampleTestCase {

    /**
     * @var \Example\Stack
     */
    public $stack;

    protected function setUp()
    {
        parent::setUp();

        $this->stack = new Stack();
    }

    /**
     * @spec
     */
    public function testSize()
    {
        expect:
        $this->stack->size() == 0;
    }

    /**
     * @spec
     */
    public function testPop()
    {
        when:
        $this->stack->pop();

        then:
        thrown('Example\Stack\EmptyStackException');
    }

    /**
     * @spec
     */
    public function testPeek()
    {
        when:
        $this->stack->peek();

        then:
        thrown('Example\Stack\EmptyStackException');
    }

    /**
     * @spec
     */
    public function testPush()
    {
        when:
        $this->stack->push("element");

        then:
        $this->stack->size() == 1;
        $this->stack->peek() == "element";
    }


}
