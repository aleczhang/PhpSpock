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
use \PhpSpock\ParseException;

class ThenBlockParser extends AbstractParser {

    /**
     * @param $code
     * @return \PhpSpock\Specification\SimpleBlock
     */
    public function parse($code) {

        $tokens = $this->tokenizeCode($code);

        $expressions = array();

        $expressionTokens = array();

        $bracesOpen = 0;
        for($i=0; $i < count($tokens); $i++) {

            switch($tokens[$i][0]) {
                case '(':
                case '{':
                    $bracesOpen++;
                    $expressionTokens[] = $tokens[$i];
                    break;
                case ')':
                case '}':
                    $bracesOpen--;
                    $expressionTokens[] = $tokens[$i];
                    break;
                case ';':

                    if ($bracesOpen == 0) {
                        $expr = new \PhpSpock\Specification\ThenBlock\Expression();
                        $code = '';
                        foreach($expressionTokens as $token) {
                            $code .= $token[1];
                        }
                        $expr->setCode(trim($code));
                        $expressionTokens = array();

                        $comment = null;
                        if (isset($tokens[$i + 1])) {
                            if (in_array($tokens[$i + 1][0], array(T_COMMENT, T_DOC_COMMENT)))
                            {
                                $comment = $tokens[$i + 1][1];
                                $i++;
                            }

                            if (isset($tokens[$i + 2])) {
                                if ($tokens[$i + 1][0] == T_WHITESPACE && in_array($tokens[$i + 2][0], array(T_COMMENT, T_DOC_COMMENT))) {
                                    $comment = $tokens[$i + 2][1];

                                    $i += 2;
                                }
                            }
                        }
                        if ($comment) {
                            if (substr($comment, 0, 2) == '//') {
                                $comment = substr($comment, 2);
                            }

                            $expr->setComment(trim($comment));
                        }
                        $expressions[] = $expr;

                        continue(2);
                    }

                default:
                    $expressionTokens[] = $tokens[$i];
            }
        }

        $block = new \PhpSpock\Specification\ThenBlock();
        $block->setExpressions($expressions);

        return $block;
    }
}
