<?php
/**
 * Date: 11/7/11
 * Time: 9:04 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\Specification\ThenBlock;
 
class Expression {

    private $code;
    private $comment;

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }
}
