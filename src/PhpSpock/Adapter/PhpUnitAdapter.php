<?php
/**
 * This file is part of PhpSpock.
 *
 * PhpSpock is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * PhpSpock is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PhpSpock.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2011 Aleksandr Rudakov <ribozz@gmail.com>
 *
 **/
/**
 * Date: 11/4/11
 * Time: 10:36 AM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock\Adapter;
use PhpSpock\Specification\AssertionException;
 
class PhpUnitAdapter implements \PhpSpock\Adapter, \Symfony\Component\EventDispatcher\EventSubscriberInterface {

    /**
     * @var \PHPUnit_Framework_TestCase
     */
    private $test;
    private $class;
    private $method;

    private static $isCleaned = array();

    /**
     * @param $test
     * @param \PhpSpock\PhpSpock $phpSpock
     * @return mixed | null
     */
    public function run($test, \PhpSpock\PhpSpock $phpSpock)
    {
        try {

            return $phpSpock->run($test);

        } catch(AssertionException $e) {
            throw new \PHPUnit_Framework_ExpectationFailedException(
                $e->getMessage()
            );
        }

    }

    public static function isSpecification(\PHPUnit_Framework_TestCase $test)
    {
        if ($test->getName(false) === NULL) {
            throw new \PHPUnit_Framework_Exception(
              'PHPUnit_Framework_TestCase::$name must not be NULL.'
            );
        }

        try {
            $class  = new \ReflectionClass($test);
            $method = $class->getMethod($test->getName(false));
            $methodName = $method->getName();



            return substr($methodName, -4) == 'Spec' || preg_match('/\s+@spec\s+/', $method->getDocComment());

        } catch (\ReflectionException $e) {
            $test->fail($e->getMessage());
        }

        return false;
    }

    public static function runTest(\PHPUnit_Framework_TestCase $test)
    {
        if ($test->getName(false) === NULL) {
            throw new \PHPUnit_Framework_Exception(
              'PHPUnit_Framework_TestCase::$name must not be NULL.'
            );
        }

        try {
            $class  = new \ReflectionClass($test);
            $method = $class->getMethod($test->getName(false));
            $methodName = $method->getName();

            $instance = new static();
            $instance->setTest($test);
            $instance->setClass($class);
            $instance->setMethod($method);

            $instance->cleanUpClass();

            $phpSpock = $instance->createPhpSpockInstance();

            $assertionCount = $phpSpock->runWithAdapter(
                $instance,
                array($test, $methodName));

            $test->addToAssertionCount($assertionCount);

        } catch (\ReflectionException $e) {
            $test->fail($e->getMessage());
        }
        catch (\Exception $e) {
            $expectedExceptionClass = $test->getExpectedException();
            if (!$e instanceof \PHPUnit_Framework_IncompleteTest &&
                !$e instanceof \PHPUnit_Framework_SkippedTest &&
                is_string($test->getExpectedException()) &&
                $e instanceof $expectedExceptionClass) {

                if (isset($methodName)) {
                    $expectedException = self::getExpectedExceptionFromAnnotation($test, $methodName);
                    if (is_string($expectedException['message']) &&
                        !empty($expectedException['message'])) {
                        $test->assertContains(
                          $expectedException['message'],
                          $e->getMessage()
                        );
                    }

                    if (is_int($expectedException['code']) &&
                        $expectedException['code'] !== 0) {
                        $test->assertEquals(
                          $expectedException['code'], $e->getCode()
                        );
                    }

                    $test->addToAssertionCount(1);

                    return;
                }
            } else {
                throw $e;
            }
        }
    }

    public function createPhpSpockInstance()
    {
        $phpSpock = new \PhpSpock\PhpSpock();

        $phpSpock->getEventDispatcher()->addSubscriber($this);
        return $phpSpock;
    }

    public function onBeforeCodeGenerationEvent(\PhpSpock\Event $event)
    {
        $code = $event->getAttribute('code');
        $code = str_replace('$this->', '$__specification__phpunit_test->', $code);
        $event->setAttribute('code', $code);
    }

    public function onTransformTestEvent(\PhpSpock\Event $event)
    {
        $exception = $event->getAttribute('exception');

        if ($exception instanceof AssertionException) {
            $msg = str_replace('$__specification__phpunit_test', '$this', $exception->getMessage());
            $exception = new AssertionException($msg);
        }

//        if ($exception instanceof \PHPUnit_Framework_ExpectationFailedException) {
//            $exception = new AssertionException($exception->getMessage());
//        }

        $event->setAttribute('exception', $exception);
    }

    public function onCollectExtraVariablesEvent(\PhpSpock\Event $event)
    {
        $event->setAttribute('__specification__phpunit_test', $this->getTest());
    }

    public function onModifyAssertCount(\PhpSpock\Event $event)
    {
        $class = get_class($this->test); //var_dump();
        
        $event->setAttribute('count',
            $event->getAttribute('count') + $class::getCount()
        );
    }

    public function onDebugEvent(\PhpSpock\Event $event)
    {
        if(preg_match('/\s+@specDebug\s+/', $this->getMethod()->getDocComment())) {

            $this->generateDebugCode($event);
            
//            $event->setAttribute('result', 0);
        }
    }

    public function generateDebugCode(\PhpSpock\Event $event)
    {
        $code = $event->getAttribute('code');

        $methodName = $this->generateDebugMethodName($event);

        $code = $this->generateDebugMethodCode($methodName, $code);
        $this->insertCodeIntoFile($this->getMethod()->getEndLine(), $code);
    }

    public function cleanUpClass()
    {
        if (isset(static::$isCleaned[$this->getClass()->getName()])) {
            return;
        }

        $partsToRemove = array();
        foreach ($this->getClass()->getMethods() as $method) {
            /**
             * @var $method \ReflectionMethod
             */
            if (substr($method->getName(), 0, 17) == 'test__spec_debug_') {
                $partsToRemove[] = array($method->getStartLine() - 2, $method->getEndLine());
            }
        }
        if (count($partsToRemove)) {
            $classCodeLines = file($this->getClass()->getFilename());
            foreach ($partsToRemove as $part) {
                for ($i = $part[0]; $i < $part[1]; $i++) {
                    unset($classCodeLines[$i]);
                }
            }
            $resultingCode = implode($classCodeLines);
            file_put_contents($this->getClass()->getFilename(), $resultingCode);
        }

        static::$isCleaned[$this->getClass()->getName()] = true;
    }

    protected function removeSliceFromFile($startLine, $endLine) {

        $classCodeLines = file($this->getClass()->getFilename());
        $before = implode(array_slice($classCodeLines, 0, $startLine));
        $after = implode(array_slice($classCodeLines, $endLine));

        file_put_contents($this->getClass()->getFilename(), $before . $after);
    }

    protected function insertCodeIntoFile($afterLine, $code) {

        $classCodeLines = file($this->getClass()->getFilename());
        $before = implode(array_slice($classCodeLines, 0, $afterLine));
        $after = implode(array_slice($classCodeLines, $afterLine));

        file_put_contents($this->getClass()->getFilename(), $before . $code . $after);
    }

    protected function generateDebugMethodCode($methodName, $code)
    {
        $code = "\n    function $methodName() { \n" . $code . "\n    }\n";
        return $code;
    }

    public function generateDebugMethodName(\PhpSpock\Event $event)
    {
        $methodName = 'test__spec_debug_' . $this->getMethod()->getName() . '_Variant' . $event->getAttribute('variant');
        return $methodName;
    }

    /**
     * @since  Method available since Release 3.4.0
     * @param $class
     * @param $methodName
     * @return \Exception | null
     */
    protected static function getExpectedExceptionFromAnnotation($class, $methodName)
    {
        try {
            return \PHPUnit_Util_Test::getExpectedException(
              get_class($class), $methodName
            );
        }

        catch (\ReflectionException $e) {
        }
        return null;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    static function getSubscribedEvents()
    {
        return array(
            \PhpSpock\Event::EVENT_BEFORE_CODE_GENERATION   => 'onBeforeCodeGenerationEvent',
            \PhpSpock\Event::EVENT_COLLECT_EXTRA_VARIABLES  => 'onCollectExtraVariablesEvent',
            \PhpSpock\Event::EVENT_TRANSFORM_TEST_EXCEPTION => 'onTransformTestEvent',
            \PhpSpock\Event::EVENT_MODIFY_ASSERTION_COUNT   => 'onModifyAssertCount',
            \PhpSpock\Event::EVENT_DEBUG                    => 'onDebugEvent'
        );
    }

    public function setTest($test)
    {
        $this->test = $test;
    }

    public function getTest()
    {
        return $this->test;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return \ReflectionClass
     */
    public function getClass()
    {
        return $this->class;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return \ReflectionMethod
     */
    public function getMethod()
    {
        return $this->method;
    }
}
