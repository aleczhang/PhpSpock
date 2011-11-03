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

    /**
     * @var \PhpSpock\Specification\SimpleBlock
     */
    protected $setupBlock;

    /**
     * @var \PhpSpock\Specification\SimpleBlock
     */
    protected $whenBlock;

    /**
     * @var \PhpSpock\Specification\ThenBlock
     */
    protected $thenBlock;

    /**
     * @var \PhpSpock\Specification\WhereBlock
     */
    protected $whereBlock;

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

    /**
     * @param \PhpSpock\Specification\SimpleBlock $setupBlock
     */
    public function setSetupBlock($setupBlock)
    {
        $this->setupBlock = $setupBlock;
    }

    /**
     * @return \PhpSpock\Specification\SimpleBlock
     */
    public function getSetupBlock()
    {
        return $this->setupBlock;
    }

    /**
     * @param \PhpSpock\Specification\ThenBlock $thenBlock
     */
    public function setThenBlock($thenBlock)
    {
        $this->thenBlock = $thenBlock;
    }

    /**
     * @return \PhpSpock\Specification\ThenBlock
     */
    public function getThenBlock()
    {
        return $this->thenBlock;
    }

    /**
     * @param \PhpSpock\Specification\SimpleBlock $whenBlock
     */
    public function setWhenBlock($whenBlock)
    {
        $this->whenBlock = $whenBlock;
    }

    /**
     * @return \PhpSpock\Specification\SimpleBlock
     */
    public function getWhenBlock()
    {
        return $this->whenBlock;
    }

    /**
     * @param \PhpSpock\Specification\WhereBlock $whereBlock
     */
    public function setWhereBlock($whereBlock)
    {
        $this->whereBlock = $whereBlock;
    }

    /**
     * @return \PhpSpock\Specification\WhereBlock
     */
    public function getWhereBlock()
    {
        return $this->whereBlock;
    }

    public function run()
    {
        $code = '';

        if ($this->setupBlock) {
            $code .= $this->setupBlock->compileCode($this);
        }
        if ($this->whenBlock) {
            $code .= $this->whenBlock->compileCode($this);
        }
        if ($this->thenBlock) {
            $code .= $this->thenBlock->compileCode($this);
        }

        // eval will be executed in it's own scope
        $func = function() use($code) {
            $_ret = null; // for testing
            eval($code);
            return $_ret;
        };

        return $func();
    }
}
