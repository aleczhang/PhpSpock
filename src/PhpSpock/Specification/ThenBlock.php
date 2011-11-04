<?php
/**
 * Date: 11/3/11
 * Time: 1:39 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
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

            $code .= '$op = (' . $expr . ');
            if (is_bool($op) && !$op) {
                $msg = "Expression '.str_replace('$', '\$', $expr).' is evaluated to false.";
                if (isset($__parametrization__lastVariants)) {
                    $msg .= "\n\nParametriazation values [step $__parametrization__step]: \n";

                    $_tbpm_longestLeft = 0;
                    foreach($__parametrization__lastVariants as $_tbpm_key => $_tbpm_value) {
                        if (strlen($_tbpm_key) > $_tbpm_longestLeft) {
                            $_tbpm_longestLeft = strlen($_tbpm_key);
                        }
                    }

                    foreach($__parametrization__lastVariants as $_tbpm_key => $_tbpm_value) {
                        $msg .= "  $_tbpm_key".str_repeat(" ", $_tbpm_longestLeft - strlen($_tbpm_key))." :  $_tbpm_value\n";
                    }
                }
                throw new \PhpSpock\Specification\AssertionException($msg);
            }';
        }
        return $code;
    }
}
