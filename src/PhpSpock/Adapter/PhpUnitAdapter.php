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
}
