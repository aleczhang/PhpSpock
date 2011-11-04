<?php
/**
 * Date: 11/3/11
 * Time: 1:39 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\Specification;

use \PhpSpock\Specification\WhereBlock\Parameterization;
 
class WhereBlock {

    /**
     * @var Parameterization[]
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
        $code = '$__parametrization__step = '.$step.'; $__parametrization__counts = array();';
        foreach($this->parametrizations as $p) {

            $code .= '$__parametrization__variants = ' . $p->getRightExpression() . ';
            $__parametrization__counts[] = count($__parametrization__variants);
            '.$p->getLeftExpression().' = $__parametrization__variants[$__parametrization__step % count($__parametrization__variants)];
            ';
        }
        $code .= '$__parametrization__hasMoreVariants = (($__parametrization__step + 1) < max($__parametrization__counts));';

        return $code;
    }
}
