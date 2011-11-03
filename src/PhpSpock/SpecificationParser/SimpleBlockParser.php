<?php
/**
 * Date: 11/3/11
 * Time: 1:29 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\SpecificationParser;
use \PhpSpock\ParseException;

class SimpleBlockParser extends AbstractParser {

    /**
     * @param $code
     * @return \PhpSpock\Specification\SimpleBlock
     */
    public function parse($code) {

        $block = new \PhpSpock\Specification\SimpleBlock();
        $block->setCode($code);

        return $block;
    }
}
