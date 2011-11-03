<?php
/**
 * Date: 11/3/11
 * Time: 5:21 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock;

 
class PhpSpock {

    public function run($test) {
        $parser = new SpecificationParser();

        $testSpec = $parser->parse($test);
        $testSpec->run();
    }
}
