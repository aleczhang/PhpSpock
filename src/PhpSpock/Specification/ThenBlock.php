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
 * Time: 1:39 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock\Specification;
 
class ThenBlock {

    /**
     * @var \PhpSpock\Specification\ThenBlock\Expression[]
     */
    private $expressions = array();

    private $preConditions = array();

    /**
     * @param $expressions
     * @return void
     */
    public function setExpressions($expressions)
    {
        $this->expressions = $expressions;
    }

    /**
     * @return array|ThenBlock\Expression[]
     */
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

        $this->preConditions = array();

        foreach($this->expressions as $expr) {

            $comment = $expr->getComment();
            $exprCode = $expr->compile();
            $expr = $expr->getCode();

            if (is_null($exprCode)) {
                return '';
            }

            $transformed = $this->getExpressionTransformer()->transform($expr);
            if ($transformed != $expr) {
                $this->preConditions[] = $transformed;
                continue;
            }

            $code .= $exprCode . '

        if (is_bool($__specification__expressionResult)) {

            if (!isset($__specification__assertCount)) {
                $__specification__assertCount = 0;
            }
            $__specification__assertCount++;

            if(!$__specification__expressionResult) {
                $__specification__msg = "Expression '.str_replace('$', '\$', addslashes($expr)).' is evaluated to false.";
                '.($comment ? '$__specification__msg .= "\n\n' . addslashes($comment) . '";' : '') .'

                throw new \PhpSpock\Specification\AssertionException($__specification__msg);
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

    public function setPreConditions($preConditions)
    {
        $this->preConditions = $preConditions;
    }

    public function getPreConditions()
    {
        return $this->preConditions;
    }

    /**
     * @return \PhpSpock\Specification\ExpressionTransformer
     */
    public function getExpressionTransformer()
    {
        return new ExpressionTransformer();
    }
}
