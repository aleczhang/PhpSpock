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
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
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
 
class PhpUnitAdapter implements \PhpSpock\Adapter {

    /**
     * @param $test
     * @return void
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

        } catch (\ReflectionException $e) {
            $test->fail($e->getMessage());
        }

        try {
            $phpSpock = new \PhpSpock\PhpSpock();

            $assertionCount = $phpSpock->runWithAdapter(
                new static(),
                array($test, $methodName));

            $test->addToAssertionCount($assertionCount);
        }
        catch (\Exception $e) {
            $expectedExceptionClass = $test->getExpectedException();
            if (!$e instanceof \PHPUnit_Framework_IncompleteTest &&
                !$e instanceof \PHPUnit_Framework_SkippedTest &&
                is_string($test->getExpectedException()) &&
                $e instanceof $expectedExceptionClass) {

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
