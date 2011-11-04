<?php
/**
 * Date: 11/4/11
 * Time: 10:36 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\Adapter;
use PhpSpock\Specification\AssertionException;
 
class PhpUnitAdapter implements \PhpSpock\Adapter {

    /**
     * @param $test
     * @return void
     */
    public function run($test, \PhpSpock\PhpSpock $phpSpock)
    {
        try {
            $phpSpock->run($test);

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
            return substr($methodName, -4) == 'Spec';

        } catch (\ReflectionException $e) {
            $test->fail($e->getMessage());
        }
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
            return substr($methodName, -4) == 'Spec';

        } catch (\ReflectionException $e) {
            $test->fail($e->getMessage());
        }

        try {
            $phpSpock = new \PhpSpock\PhpSpock();

            $phpSpock->runWithAdapter(
                new static(),
                array($test, $methodName));
        }
        catch (\Exception $e) {
            if (!$e instanceof \PHPUnit_Framework_IncompleteTest &&
                !$e instanceof \PHPUnit_Framework_SkippedTest &&
                is_string($test->getExpectedException()) &&
                $e instanceof $test->getExpectedException()) {

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
            } else {
                throw $e;
            }
        }
    }

    /**
     * @since  Method available since Release 3.4.0
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
}
