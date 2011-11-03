<?php
/**
 * Date: 11/3/11
 * Time: 1:29 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\SpecificationParser;
use \PhpSpock\ParseException;

class ThenBlockParser extends AbstractParser {

    /**
     * @param $code
     * @return \PhpSpock\Specification\ThenBlock
     */
    public function parse($code) {

        $lines = $this->splitCodeExpressions($code, false);

        $block = new \PhpSpock\Specification\ThenBlock();
        $block->setExpressions($lines);

        return $block;
    }
}
