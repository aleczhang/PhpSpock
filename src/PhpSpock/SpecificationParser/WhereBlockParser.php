<?php
/**
 * Date: 11/3/11
 * Time: 1:29 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\SpecificationParser;

use \PhpSpock\Specification\WhereBlock\Parameterization;
use \PhpSpock\ParseException;

class WhereBlockParser extends AbstractParser {

    /**
     * @throws \PhpSpock\ParseException
     * @param $code
     * @return \PhpSpock\Specification\WhereBlock
     */
    public function parse($code) {

        $lines = $this->splitCodeExpressions($code);

        $params = array();

        for($i=0; $i < count($lines); $i++) {
            $line = $lines[$i];

            if (strpos($line, '<<') !== false) {

                $this->parseArrayNotation($line, $params);

            } elseif(strpos($line, '|') !== false) {

                $header = explode('|', $line);
                $data = array();
                foreach ($header as $key => $col) {
                    $data[$key] = array();
                    $header[$key] = trim($col);
                }
                $columnCount = sizeof($header);

                $i++;
                while(isset($lines[$i]) && trim($lines[$i]) != '') {
                    $curTableLine = $lines[$i];
                    $row = explode('|', $curTableLine);
                    if (sizeof($row) != $columnCount) {
                        throw new ParseException('Table row contains different count of columns: "'.$lines[$i].'"');
                    }
                    foreach ($row as $key => $col) {
                        $data[$key][] = trim($col);
                    }
                    $i++;
                }
                $i--;
                foreach($header as $key => $leftExpr) {

                    $rightExpr = 'array(' . implode(',', $data[$key]) .')';

                    $p = new Parameterization();
                    $p->setLeftExpression(trim($leftExpr));
                    $p->setRightExpression(trim($rightExpr));

                    $params[] = $p;
                }

            } elseif (trim($line) == '') {
                continue;
            } else {
                throw new ParseException('Block "where:" may contain only parametrizations!');
            }
        }

        $block = new \PhpSpock\Specification\WhereBlock();
        $block->setParametrizations($params);

        return $block;
    }

    protected function parseArrayNotation($line, &$params)
    {
        list($left, $right) = explode('<<', $line);
        $p = new Parameterization();
        $p->setLeftExpression(trim($left));
        $p->setRightExpression(trim($right));

        $params[] = $p;
    }
}
