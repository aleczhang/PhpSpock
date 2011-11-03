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
}
