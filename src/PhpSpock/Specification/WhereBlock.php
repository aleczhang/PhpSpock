<?php
/**
 * Date: 11/3/11
 * Time: 1:39 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\Specification;
 
class WhereBlock {

    private $parametrizations;


    public function setParametrizations($parametrizations)
    {
        $this->parametrizations = $parametrizations;
    }

    public function getParametrizations()
    {
        return $this->parametrizations;
    }
}
