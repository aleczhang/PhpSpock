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
 * Time: 1:39 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock\Specification;

use \PhpSpock\Specification\WhereBlock\Parameterization;
 
class WhereBlock {

    /**
     * @var \PhpSpock\Specification\WhereBlock\Parameterization[]
     */
    private $parametrizations;

    public function setParametrizations($parametrizations)
    {
        $this->parametrizations = $parametrizations;
    }

    /**
     * @return Parameterization[]
     */
    public function getParametrizations()
    {
        return $this->parametrizations;
    }

    public static function clazz() {
        return get_called_class();
    }

    public function compileCode($step)
    {
        $code = '
        $__parametrization__step = '.$step.';
        $__parametrization__counts = array();
        $__parametrization__lastVariants = array();
        $__parametrization__lastValues = array();';

        foreach($this->parametrizations as $p) {

            $code .= '

        // Parametrization set: ' . $p->getLeftExpression() . ' << ' . $p->getRightExpression() . '

            // calculate values
            $__parametrization__variants = ' . $p->getRightExpression() . ';
            $__parametrization__counts[] = count($__parametrization__variants);
            $__parametrization__elId = $__parametrization__step % count($__parametrization__variants);

            // assign variables
            '.$p->getLeftExpression().' = $__parametrization__variants[$__parametrization__elId];

            // info for exceptions
            $__parametrization__lastVariants[\''.addslashes($p->getLeftExpression()).'\'] = \'element[\' . $__parametrization__elId .\'] of array: '.addslashes($p->getRightExpression()).'\';
            $__parametrization__lastValues[\''.addslashes($p->getLeftExpression()).'\'] = var_export('.$p->getLeftExpression().', 1);

        // End of parametrization set
        ';
        }
        $code .= '

        $__parametrization__hasMoreVariants = (($__parametrization__step + 1) < max($__parametrization__counts));';

        return $code;
    }
}
