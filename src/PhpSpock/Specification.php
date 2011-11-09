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

    protected $varDeclarations = array();

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;

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

    /**
     * @var \PhpSpock\Specification\SimpleBlock
     */
    protected $cleanupBlock;

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

    protected function attachBlockCode($blockName, $code)
    {
        $formatedCode ="
        /**
         * $blockName block
         */
        $code

";
        return $formatedCode;
    }

    public function run()
    {
        $ret = null;
        $stepCounter = 1;
        $hasMoreVariants = true;

        $__specification__assertCount = 0;

        while($hasMoreVariants) {

            $code = '';

            if (count($this->varDeclarations)) {
                $code .= '
        /**
         * Mocks
         */
         ';
            }
            // generate mocks
            foreach($this->varDeclarations as $varName => $varType) {
                $code .= '
        $'.$varName.' = \Mockery::mock(\''.$varType.'\');';
            }
            if (count($this->varDeclarations)) {
                $code .= '

                ';
            }

            if ($this->setupBlock) {
                $code .= $this->attachBlockCode('Setup', $this->setupBlock->compileCode());
            }
            if ($this->whereBlock) {
                $code .= $this->attachBlockCode('Where', $this->whereBlock->compileCode($stepCounter));
            }

            foreach($this->whenThenPairs as $pair) {
                if ($pair->getWhenBlock()) {
                    $code .= $this->attachBlockCode('When', $pair->getWhenBlock()->compileCode());
                }
                if ($pair->getThenBlock()) {
                    $code .= $this->attachBlockCode('Then', $pair->getThenBlock()->compileCode());
                }
            }

            foreach($this->varDeclarations as $varName => $varType) {
                $code .= '
        try {
            $'.$varName.'->mockery_verify();
            $'.$varName.'->mockery_teardown();
        } catch (\Exception $e) {
            $msg = "Mock \$'.$varName.' validation exception: " . $e->getMessage();
            throw new \PhpSpock\Specification\AssertionException($msg);
        }
                ';
            }

            if ($this->cleanupBlock) {
                $code .= $this->attachBlockCode('Cleanup', $this->cleanupBlock->compileCode());
            }

            $event = new Event();
            $event->setAttribute('code', $code);
            $event->setAttribute('variant', $stepCounter);
            $this->getEventDispatcher()->dispatch(Event::EVENT_DEBUG, $event);
            $debugResult = $event->getAttribute('result');
            if ($debugResult) {
                return $debugResult;
            }

            if (count($this->useStatements)) {
                foreach($this->useStatements as $alias => $class) {
                    $code = "use $class as $alias;\n" . $code;
                }
            }

            if ($this->namespace != '') {
                $code = 'namespace '. $this->getNamespace() . ' { ' . $code . "\n}";
            }


            $code = $this->preprocessCode($code);

            $extraVars = $this->getExtraVars();

            $__eventDispatcher = $this->getEventDispatcher();

            // eval will be executed in it's own scope
            $func = function() use($code, &$__specification__assertCount, $extraVars, $__eventDispatcher) {
                
                extract($extraVars);

                $__parametrization__hasMoreVariants = false;
                $__parametrization__lastVariants = null;
                $__parametrization__lastValues = null;
//                $__specification__debug_output = '';

                $_ret = null; // for testing

//                  var_dump($code);

                try {
                    eval($code);

                } catch(\Exception $e) {

                    $event = new Event();
                    $event->setAttribute('exception', $e);
                    
                    $__eventDispatcher->dispatch(Event::EVENT_TRANSFORM_TEST_EXCEPTION, $event);

                    $e = $event->getAttribute('exception');

                    if ($e instanceof AssertionException) {
                        
                        $msg = $e->getMessage();

                        if (isset($__parametrization__lastVariants)) {

                            $_tbpm_longestLeft = 0;
                            foreach($__parametrization__lastVariants as $_tbpm_key => $_tbpm_value) {
                                if (strlen($_tbpm_key) > $_tbpm_longestLeft) {
                                    $_tbpm_longestLeft = strlen($_tbpm_key);
                                }
                            }

                            $msg .= "\n\n Where: \n---------------------------------------------------\n";

                            foreach($__parametrization__lastValues as $_tbpm_key => $_tbpm_value) {
                                $msg .= "  $_tbpm_key".str_repeat(" ", $_tbpm_longestLeft - strlen($_tbpm_key))." :  $_tbpm_value\n";
                            }

                            $msg .= "\n\n Parametriazation values [step $__parametrization__step]:  \n---------------------------------------------------\n";

                            foreach($__parametrization__lastVariants as $_tbpm_key => $_tbpm_value) {
                                $msg .= " $_tbpm_key".str_repeat(" ", $_tbpm_longestLeft - strlen($_tbpm_key))." :  $_tbpm_value\n";
                            }
                        }

//                        if ($__specification__debug_output != '') {
//                            $msg .= "\n\n Debug output: \n---------------------------------------------------\n";
//                            $msg .= $__specification__debug_output;
//                            $msg .= "\n---------------------------------------------------\n";
//                        }

                        throw new AssertionException($msg);
                    }

                    if ($e instanceof \Exception) {
                        throw $e;
                    }
                }

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

    public function getExtraVars()
    {
        $event = new Event();
        $this->getEventDispatcher()->dispatch(Event::EVENT_COLLECT_EXTRA_VARIABLES, $event);
        $extraVars = $event->getAllAttributes();
        return $extraVars;
    }

    public function preprocessCode($code)
    {
        $event = new Event();
        $event->setAttribute('code', $code);

        $this->getEventDispatcher()->dispatch(Event::EVENT_BEFORE_CODE_GENERATION, $event);

        $code = $event->getAttribute('code');
        return $code;
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

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if (!$this->eventDispatcher) {
            $this->eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
        }
        return $this->eventDispatcher;
    }

    /**
     * @param \PhpSpock\Specification\SimpleBlock $cleanupBlock
     */
    public function setCleanupBlock($cleanupBlock)
    {
        $this->cleanupBlock = $cleanupBlock;
    }

    /**
     * @return \PhpSpock\Specification\SimpleBlock
     */
    public function getCleanupBlock()
    {
        return $this->cleanupBlock;
    }

    public function setVarDeclarations($varDeclarations)
    {
        $this->varDeclarations = $varDeclarations;
    }

    public function getVarDeclarations()
    {
        return $this->varDeclarations;
    }
}
