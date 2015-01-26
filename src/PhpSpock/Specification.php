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
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
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
     * @var \PhpSpock\Specification\SetupBlock
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
        $stepCounter = 0;
        $hasMoreVariants = true;

        $__specification__assertCount = 0;

        while($hasMoreVariants) { // loop until we have parameterization variants

            $__specification__code = '';

            $__specification__code .= $this->buildMocksDeclaration();

            if ($this->setupBlock) {
                $__specification__code .= $this->buildSetupCode();
            }
            if ($this->whereBlock) {
                $__specification__code .= $this->buildParametrizationCode($stepCounter);
            }

            $__specification__code .= $this->buildWhenThenPairsCode();

            if ($this->cleanupBlock) {
                $__specification__code .= $this->buildCleanupCode();
            }

            $debugResult = $this->tryToUseDebugger($__specification__code, $stepCounter);

            // debug mode
            if ($this->debuggerHasReturnedSomeNotNullResult($debugResult)) {
                return $debugResult;
            }

            if ($this->haveAnyUseStatementDeclarations()) {
                $__specification__code = $this->wrapCodeWithUseDeclarationsCode($__specification__code);
            }

            if ($this->namespaceIsNotDefault()) {
                $__specification__code = $this->wrapCodeWithNamespaceDeclaration($__specification__code);
            }

            $__specification__code = $this->giveEventListenersAChanceToModifyCode($__specification__code);

            $hasMoreVariants = $this->executeGeneratedCode($__specification__assertCount, $__specification__code);

            $stepCounter++;
        }

        $__specification__assertCount = $this->giveEventListenersAChanceToModifyAssertionCount($__specification__assertCount);

        $assertionCount = $__specification__assertCount;
        
        if (!is_numeric($assertionCount)) {
            throw new TestExecutionException('Assertion count variable is corrupted!');
        }
        if ($assertionCount == 0) {
            throw new \PhpSpock\Specification\AssertionException('Block "then:" does not contain any assertions.');
        }

        return $assertionCount;
    }

    public function executeGeneratedCode(&$__specification__assertCount, $__specification__code)
    {
        $__specification__extraVars = $this->getExtraVars();
        $__specification__eventDispatcher = $this->getEventDispatcher();

        // eval will be executed in it's own scope
        $func = function() use($__specification__code, &$__specification__assertCount, $__specification__extraVars, $__specification__eventDispatcher)
        {

            extract($__specification__extraVars);

            $__parametrization__hasMoreVariants = false;
            $__parametrization__lastVariants = null;
            $__parametrization__lastValues = null;

            try {
                eval($__specification__code);

            } catch (\Exception $__specification__currentException) {

                $__specification__currentEvent = new Event();
                $__specification__currentEvent->setAttribute('exception', $__specification__currentException);

                /**
                 * @var $__specification__eventDispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface
                 */
                $__specification__eventDispatcher->dispatch(Event::EVENT_TRANSFORM_TEST_EXCEPTION, $__specification__currentEvent);

                $__specification__currentException = $__specification__currentEvent->getAttribute('exception');

                if ($__specification__currentException instanceof AssertionException) {

                    $__specification__msg = $__specification__currentException->getMessage();

                    if (isset($__parametrization__lastVariants)) {

                        $_tbpm_longestLeft = 0;
                        foreach ($__parametrization__lastVariants as $_tbpm_key => $_tbpm_value) {
                            if (strlen($_tbpm_key) > $_tbpm_longestLeft) {
                                $_tbpm_longestLeft = strlen($_tbpm_key);
                            }
                        }

                        $__specification__msg .= "\n\n Where: \n---------------------------------------------------\n";

                        foreach ($__parametrization__lastValues as $_tbpm_key => $_tbpm_value) {
                            $__specification__msg .= "  $_tbpm_key" . str_repeat(" ", $_tbpm_longestLeft - strlen($_tbpm_key)) . " :  $_tbpm_value\n";
                        }

                        $__specification__msg .= "\n\n Parametriazation values [step $__parametrization__step]:  \n---------------------------------------------------\n";

                        foreach ($__parametrization__lastVariants as $_tbpm_key => $_tbpm_value) {
                            $__specification__msg .= " $_tbpm_key" . str_repeat(" ", $_tbpm_longestLeft - strlen($_tbpm_key)) . " :  $_tbpm_value\n";
                        }
                    }

                    $__specification__varsDeclared = array();
                    foreach (get_defined_vars() as $varName => $var_value) {
                        if (strpos($varName, '__specification') === 0 || strpos($varName, '__parametrization') === 0
                            || strpos($varName, '_tbpm_') === 0) {
                            continue;
                        }
                        $__specification__varsDeclared[$varName] = $var_value;
                    }
                    if (count($__specification__varsDeclared)) {
                        $__specification__msg .= "\n\n Declared variables: \n---------------------------------------------------\n";
                        $longest = 0;
                        foreach ($__specification__varsDeclared as $name => $value) {
                            if (strlen($name) > $longest) {
                                $longest = strlen($name);
                            }
                        }
                        foreach ($__specification__varsDeclared as $name => $value) {
                            $__specification__msg .= " \$$name ";
                            $__specification__msg .= str_repeat(' ', $longest - strlen($name)) . ' : ';

                            if ($value instanceof \Mockery\MockInterface) {
                                /**
                                 * @var $value \Mockery\MockInterface
                                 */
                                $__specification__msg .= 'Mock for ' . $value->mockery_getName();
                            }
                            elseif (is_object($value)) {
                                $__specification__msg .= 'instance of ' . get_class($value);
                            }
                            elseif (is_array($value)) {
                               $__specification__msg .= 'Array with size ' . count($value);
                            }
                            else {
                                $__specification__msg .= $value;
                            }
                            $__specification__msg .= "\n";
                        }
                        $__specification__msg .= "\n---------------------------------------------------\n";
                    }
                    throw new AssertionException($__specification__msg);
                }

                if ($__specification__currentException instanceof \Exception) {
                    throw $__specification__currentException;
                }
            }

            return $__parametrization__hasMoreVariants;
        };

        return $func();
    }

    public function wrapCodeWithNamespaceDeclaration($__specification__code)
    {
        return 'namespace ' . $this->getNamespace() . ' { ' . $__specification__code . "\n}";
    }

    public function namespaceIsNotDefault()
    {
        return $this->namespace != '';
    }

    public function wrapCodeWithUseDeclarationsCode($code)
    {
        foreach ($this->useStatements as $alias => $class) {
            $code = "use $class as $alias;\n" . $code;
        }
        return $code;
    }

    public function haveAnyUseStatementDeclarations()
    {
        return count($this->useStatements);
    }

    public function debuggerHasReturnedSomeNotNullResult($debugResult)
    {
        return $debugResult != null;
    }

    protected function tryToUseDebugger($__specification__code, $stepCounter)
    {
        $event = new Event();
        $event->setAttribute('code', $__specification__code);
        $event->setAttribute('variant', $stepCounter);
        $this->getEventDispatcher()->dispatch(Event::EVENT_DEBUG, $event);
        $debugResult = $event->getAttribute('result');
        return $debugResult;
    }

    public function buildCleanupCode()
    {
        return $this->attachBlockCode('Cleanup', $this->cleanupBlock->compileCode());
    }

    public function buildWhenThenPairsCode()
    {
        $code = '';
        foreach ($this->whenThenPairs as $pair) {

            $thenCode = '';
            if ($pair->getThenBlock()) {
                $thenCode = $this->attachBlockCode('Then', $pair->getThenBlock()->compileCode());

                $preconditions = $pair->getThenBlock()->getPreConditions();
                if (count($preconditions)) {
                    foreach ($preconditions as $precondition) {
                        $code .= '
        ' . $precondition . ';';
                    }
                    $code .= '
        if (!isset($__specification__assertCount)) {
            $__specification__assertCount = 0;
        }
        $__specification__assertCount += ' . count($preconditions) . ';';
                }

                foreach ($this->varDeclarations as $varName => $varType) {
                    $thenCode .= '
        try {
            $' . $varName . '->mockery_verify();
            $' . $varName . '->mockery_teardown();
        } catch (\Exception $__specification__e) {
            $__specification__msg = "Mock \$' . $varName . ' validation exception: " . $__specification__e->getMessage();
            throw new \PhpSpock\Specification\AssertionException($__specification__msg);
        }
                ';
                }
            }

            if ($pair->getWhenBlock()) {
                $code .= $this->attachBlockCode('When', $pair->getWhenBlock()->compileCode());
            }
            $code .= $thenCode;
        }
        return $code;
    }

    public function buildParametrizationCode($stepCounter)
    {
        return $this->attachBlockCode('Where', $this->whereBlock->compileCode($stepCounter));
    }

    public function buildSetupCode()
    {
        return $this->attachBlockCode('Setup', $this->setupBlock->compileCode());
    }

    protected function buildMocksDeclaration()
    {
        $code = '';
        if (count($this->varDeclarations)) {
            $code .= '
        /**
         * Mocks
         */
         ';
        }
        // generate mocks
        foreach ($this->varDeclarations as $varName => $varType) {
            $code .= '
        $' . $varName . ' = \Mockery::mock(\'' . $varType['class'] . '\')';
            if ($varType['type'] === 'Spy') {
                $code .= '->makePartial();';
            } else {
                $code .= ';';
            }
        }
        if (count($this->varDeclarations)) {
            $code .= '

                ';
            return $code;
        }
        return $code;
    }

    public function getExtraVars()
    {
        $event = new Event();
        $this->getEventDispatcher()->dispatch(Event::EVENT_COLLECT_EXTRA_VARIABLES, $event);
        $extraVars = $event->getAllAttributes();
        return $extraVars;
    }

    public function giveEventListenersAChanceToModifyCode($code)
    {
        $event = new Event();
        $event->setAttribute('code', $code);

        $this->getEventDispatcher()->dispatch(Event::EVENT_BEFORE_CODE_GENERATION, $event);

        $code = $event->getAttribute('code');
        return $code;
    }

    public function giveEventListenersAChanceToModifyAssertionCount($assertionCount)
    {
        $event = new Event();
        $event->setAttribute('count', $assertionCount);

        $this->getEventDispatcher()->dispatch(Event::EVENT_MODIFY_ASSERTION_COUNT, $event);

        return $event->getAttribute('count');
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
}
