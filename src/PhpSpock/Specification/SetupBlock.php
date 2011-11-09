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
 * Date: 11/3/11
 * Time: 1:39 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock\Specification;

use PhpSpock\ParseException;
 
class SetupBlock {

    private $expressions = array();

    public function setExpressions($expressions)
    {
        $this->expressions = $expressions;
    }

    public function getExpressions()
    {
        return $this->expressions;
    }

    public static function clazz() {
        return get_called_class();
    }

    public function compileCode()
    {
        $code = '';
        foreach($this->expressions as $expr) {

            if (preg_match('/^
                \s*

                (?P<cardinality>
                    \d+ | \(\d*_?\s*\.\.\s*\d*_?\)
                )

                \s+\*\s+

                \$(?P<var>
                    [a-zA-Z0-9_]+
                )
                ->(?P<method>
                    [a-zA-Z0-9_]+
                )

                \(\s*(?P<arguments>.*)\s*\)

                (\s+>>\s+(?P<return>
                    .*
                ))?

                \s*
                $/x', $expr, $mts)) {

                $mockExpr = '';
                $mockExpr .= '$'.$mts['var'].'->shouldReceive("'.$mts['method'].'")';

                $mockExpr .= $this->transformMockArguments($mts['arguments']);
                $mockExpr .= $this->transformMockCardinality($mts['cardinality']);



                if (isset($mts['return'])) {
                    $mockExpr .= $this->transformMockReturn($mts['return']);
                }

                $code .= $mockExpr . ';';

            } else {
                $code .= $expr . ';';
            }
        }
        return $code;
    }

    private function transformMockCardinality($expr)
    {
        if (is_numeric($expr)) {
            if ($expr == '0') {
                return '->never()';
            }
            if ($expr == '1') {
                return '->once()';
            }
            if ($expr == '2') {
                return '->twice()';
            }

            return '->times('.$expr.')';
        } else {
            if (preg_match('/^\(0\s*\.\.\s*_\)$/', $expr, $mts)) {
                return '->zeroOrMoreTimes()';
            }
            if (preg_match('/^\((?P<min>\d+)\s*\.\.\s*_\)$/', $expr, $mts)) {
                return '->atLeast()->times('.$mts['min'].')';
            }
            if (preg_match('/^\(_\s*\.\.\s*(?P<max>\d+)\)$/', $expr, $mts)) {
                return '->atMost()->times('.$mts['max'].')';
            }
            if (preg_match('/^\((?P<min>\d+)\s*\.\.\s*(?P<max>\d+)\)$/', $expr, $mts)) {
                return '->between('.$mts['min'].', '.$mts['max'].')';
            }

            throw new ParseException("Can not parse cardinality for mock object: " . $expr);
        }

    }

    private function transformMockReturn($expr)
    {
        return '->andReturn('.$expr.')';
    }

    private function transformMockArguments($expr)
    {
        if (trim($expr) == '') {
            return '->withNoArgs()';
        }

        if (trim($expr) == '_*_') {
            return '->withAnyArgs()';
        }

        $args = array();

        foreach($this->splitArgs($expr) as $argExpr) {
            $argExpr = trim($argExpr);

            if ($argExpr == '_') {
                $args[] = '\Mockery::any()';
            } elseif ($argExpr == '!null') {
                $args[] = '\Mockery::not(null)';
            } elseif ($argExpr == 'null') {
                $args[] = '\Mockery::mustBe(null)';
            } elseif (preg_match('/^(?P<method>[a-zA-Z0-9_]+)\((?P<args>.*)\)$/', $argExpr, $mts)) {
                $args[] = '\Mockery::' . $mts['method'] . '(' . $mts['args'] . ')';
            } else {
                $args[] = $argExpr;
            }
        }

        return '->with(' . implode(', ', $args) . ')';
    }

    private function splitArgs($expr)
    {
        $tokens = token_get_all('<?php ' . $expr);

        $args = array();
        $current = '';
        $bracesOpen = 0;
        foreach ($tokens as $token) {
            if (!is_array($token)) {
                $token = array($token, $token);
            }

            switch ($token[0]) {
                case T_OPEN_TAG:
                    continue(2);

                case '(':
                case '{':
                    $bracesOpen++;
                    $current .= $token[1];
                    break;
                case ')':
                case '}':
                    $bracesOpen--;
                    $current .= $token[1];
                    break;
                case ',':
                    if ($bracesOpen == 0) {
                        $args[] = trim($current);
                        $current = '';
                        break;
                    }
                default:
                    $current .= $token[1];
            }
        }
        if (trim($current) != '') {
            $args[] = trim($current);
            return $args;
        }
        return $args;
    }
}
