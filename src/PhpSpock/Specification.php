<?php
/**
 * Date: 11/3/11
 * Time: 11:19 AM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock;
 
class Specification {

    protected $rawBody;
    protected $rawBlocks = array();

    public function setRawBody($rawBody)
    {
        $this->rawBody = $rawBody;
    }

    public function getRawBody()
    {
        return $this->rawBody;
    }

    public function setRawBlocks($rawBlocks)
    {
        $this->rawBlocks = $rawBlocks;
    }

    public function getRawBlocks()
    {
        return $this->rawBlocks;
    }
}
