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
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PhpSpock.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Copyright 2011 Aleksandr Rudakov <ribozz@gmail.com>
 *
 **/
/**
 * Date: 11/3/11
 * Time: 11:19 AM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock;

use \PhpSpock\Specification\AssertionException;
 
class Specification {

    protected $rawBody;
    protected $rawBlocks = array();

    protected $file;
    protected $startLine;
    protected $endLine;

    protected $namespace;
    protected $useStatements;

    /**
     * @var Specification\WhenThenPair[]
     */
    protected $whenThenPairs = array();
    
    /**
     * @var \PhpSpock\Specification\SimpleBlock
     */
    protected $setupBlock;

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
        $ret = null;
        $stepCounter = 1;
        $hasMoreVariants = true;

        $__specification__assertCount = 0;

        while($hasMoreVariants) {

            $code = '';

            if ($this->setupBlock) {
                $code .= $this->setupBlock->compileCode();
            }
            if ($this->whereBlock) {
                $code .= $this->whereBlock->compileCode($stepCounter);
            }

            foreach($this->whenThenPairs as $pair) {
                if ($pair->getWhenBlock()) {
                    $code .= $pair->getWhenBlock()->compileCode();
                }
                if ($pair->getThenBlock()) {
                    $code .= $pair->getThenBlock()->compileCode();
                }
            }

            if (count($this->useStatements)) {
                foreach($this->useStatements as $alias => $class) {
                    $code = "use $class as $alias;\n" . $code;
                }
            }

            if ($this->namespace != '') {
                $code = 'namespace '. $this->getNamespace() . ' { ' . $code . "\n}";


            }



            // eval will be executed in it's own scope
            $func = function() use($code, &$__specification__assertCount) {

                $__parametrization__hasMoreVariants = false;
                $__parametrization__lastVariants = null;
                
                $_ret = null; // for testing

//                var_dump($code);
                eval($code);

                return $__parametrization__hasMoreVariants;
            };

            $hasMoreVariants =  $func();
            $stepCounter++;
        }

        $assertionCount = $__specification__assertCount;
        
        if (!is_numeric($assertionCount)) {
            throw new TestExecutionException('Assertion count variable is corrupted!');
        }
        if ($assertionCount == 0) {
            throw new \PhpSpock\Specification\AssertionException('Block "then:" does not contain any assertions.');
        }

        return $assertionCount;
    }

    public function setEndLine($endLine)
    {
        $this->endLine = $endLine;
    }

    public function getEndLine()
    {
        return $this->endLine;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setStartLine($startLine)
    {
        $this->startLine = $startLine;
    }

    public function getStartLine()
    {
        return $this->startLine;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function setUseStatements($useStatements)
    {
        $this->useStatements = $useStatements;
    }

    public function getUseStatements()
    {
        return $this->useStatements;
    }

    public function setWhenThenPairs($whenThenPairs)
    {
        $this->whenThenPairs = $whenThenPairs;
    }

    public function getWhenThenPairs()
    {
        return $this->whenThenPairs;
    }
}
