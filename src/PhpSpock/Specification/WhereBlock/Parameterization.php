<?php
/**
 * Date: 11/3/11
 * Time: 1:39 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\Specification\WhereBlock;
 
class Parameterization {

    private $leftExpression;
    private $rightExpression;

    public function setLeftExpression($leftExpression)
    {
        $this->leftExpression = $leftExpression;
    }

    public function getLeftExpression()
    {
        return $this->leftExpression;
    }

    public function setRightExpression($rightExpression)
    {
        $this->rightExpression = $rightExpression;
    }

    public function getRightExpression()
    {
        return $this->rightExpression;
    }
}
