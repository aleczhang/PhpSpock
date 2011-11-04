<?php
/**
 * Date: 11/4/11
 * Time: 10:36 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock;

interface Adapter {

    /**
     * @abstract
     * @param $test
     * @return void
     */
    public function run($test, PhpSpock $phpSpock);
}
