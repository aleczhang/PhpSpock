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
 * Date: 11/3/11
 * Time: 1:29 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
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
