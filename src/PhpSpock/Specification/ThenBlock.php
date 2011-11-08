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
 
class ThenBlock {

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

            $comment = $expr->getComment();
            $exprCode = $expr->compile();
            $expr = $expr->getCode();
            
            $code .= '

        ' . $exprCode . '

        if (is_bool($expressionResult)) {

            if (!isset($__specification__assertCount)) {
                $__specification__assertCount = 0;
            }
            $__specification__assertCount++;

            if(!$expressionResult) {
                $msg = "Expression '.str_replace('$', '\$', addslashes($expr)).' is evaluated to false.";
                '.($comment ? '$msg .= "\n\n' . addslashes($comment) . '";' : '') .'

                throw new \PhpSpock\Specification\AssertionException($msg);
            }
        }';
        }

        $code .= '
        if (isset($__specification_Exception) && $__specification_Exception instanceof \Exception) {
            throw $__specification_Exception;
        }
        ';

        return $code;
    }
}
