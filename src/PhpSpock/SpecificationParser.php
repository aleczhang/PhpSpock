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
 * Time: 10:38 AM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock;

use PhpSpock\SpecificationParser\AbstractParser;

use \PhpSpock\SpecificationParser\SetupBlockParser;
use \PhpSpock\SpecificationParser\SimpleBlockParser;
use \PhpSpock\SpecificationParser\WhenBlockParser;
use \PhpSpock\SpecificationParser\WhereBlockParser;
use \PhpSpock\SpecificationParser\ThenBlockParser;
 
class SpecificationParser extends AbstractParser {

    protected $allowedBlocks = array('setup', 'when', 'then', 'cleanup', 'where');

    /**
     * @param $callback
     * @return null|Specification
     */
    public function parse($callback) {

        if ($callback instanceof \Closure) {
            return $this->parseClosure($callback);
        }

        if (is_array($callback) && count($callback) == 2) {
            return $this->parseMethod($callback[0], $callback[1]);
        }

        return null;
    }


    private function parseMethod($class, $method)
    {
        $refl = new \ReflectionMethod($class, $method);
//        var_dump($refl->getFileName());
//        var_dump($refl->getStartLine());
//        var_dump($refl->getEndLine());

        $body = trim(implode(array_slice(file($refl->getFileName()), $refl->getStartLine(), $refl->getEndLine() - $refl->getStartLine() - 1)));
        
        $spec = $this->createSpecification($body, $refl->getFileName(), $refl->getStartLine(), $refl->getEndLine());

        return $spec;
    }

    private function parseClosure(\Closure $callback)
    {
        $refl = new \ReflectionFunction($callback);
//        var_dump($refl->getFileName());
//        var_dump($refl->getStartLine());
//        var_dump($refl->getEndLine());

        $body = trim(implode(array_slice(file($refl->getFileName()), $refl->getStartLine(), $refl->getEndLine() - $refl->getStartLine() - 1)));

        $spec = $this->createSpecification($body, $refl->getFileName(), $refl->getStartLine(), $refl->getEndLine());

        return $spec;
    }

    private function createSpecification($body, $fileName, $lineStart, $lineEnd)
    {
        $body = preg_replace('/^\s*{/', '', $body); // fix for { on next line after function()

        try {
            list($tDocComments, $blocks) = $this->splitOnBlocks($body);

        } catch(\Exception $e) {
            $newEx = new ParseException('Can not parse function defined in file ' . $fileName .' on line '. $lineStart
                . "\n". get_class($e) .': ' . $e->getMessage(), 0, $e);

            throw $newEx;
        }

        $parser = new \PhpSpock\SpecificationParser\FileUseTokensParser();
        list($namespace, $useStatements) = $parser->parseFile($fileName);

        $spec = new Specification();
        $spec->setVarDeclarations(
            $this->parseMockVars($tDocComments)
        );
        $spec->setNamespace($namespace);
        $spec->setUseStatements($useStatements);
        $spec->setFile($fileName);
        $spec->setStartLine($lineStart);
        $spec->setEndLine($lineEnd);
        $spec->setRawBody($body);
        $spec->setRawBlocks($blocks);
        $this->parseBlocks($blocks, $spec);
        return $spec;
    }

    private function parseMockVars($tDocComments)
    {
        $vars = array();
        if (preg_match_all('/@var\s+\$([a-zA-Z0-9_\\\]+)(\s+)?([a-zA-Z0-9_\\\]+)?\s+\*(Mock|Spy)\*\s+/', $tDocComments, $mts)) {
            foreach ($mts[1] as $index => $varName) {
                $vars[$varName] = array(
                    'type' => $mts[4][$index] === 'Mock' ? 'Mock' : 'Spy',
                    'class' => $mts[3][$index]
                );
            }
        }
        return $vars;
    }

    public function parseBlocks($blocks, Specification $spec)
    {
        $pairs = array();
        $currentPair = null;

        for ($i = 0; $i < count($blocks); $i++) {

            $block = $blocks[$i];
            $blockName = $block['name'];
            $blockCode = $block['code'];

            if (in_array($blockName, array('when', 'then'))) {

                if (!$currentPair) {
                    $currentPair = new \PhpSpock\Specification\WhenThenPair();
                }

                if ($blockName == 'when') {
                    $parser = new WhenBlockParser();
                    $currentPair->setWhenBlock($parser->parse($blockCode));
                }
                if ($blockName == 'then') {

                    $parser = new ThenBlockParser();
                    $currentPair->setThenBlock($parser->parse($blockCode));

                    $pairs[] = $currentPair;
                    $currentPair = null;
                }


            } else {

                switch ($blockName) {

                    case 'setup':
                        $parser = new SetupBlockParser();
                        $spec->setSetupBlock($parser->parse($blockCode));
                        break;

                    case 'cleanup':
                        $parser = new SimpleBlockParser();
                        $spec->setCleanupBlock($parser->parse($blockCode));
                        break;

                    case 'where':
                        $parser = new WhereBlockParser();
                        $spec->setWhereBlock($parser->parse($blockCode));
                        break;
                }
            }
        }

        if (count($pairs)) {
            $spec->setWhenThenPairs($pairs);
        }
    }

    private function splitOnBlocks($body)
    {
        $allTokens = $this->tokenizeCode($body);

        // cut off setup: label

        $blockHeaderSequence = array(T_STRING, ':', T_WHITESPACE);
        $blockSequence = 0;
        $blockSequenceData = array();
        $blockTokens = array();
        $currentBlockName = 'setup';

        $suggestedBlockName = '';

        $tDocComments = '';

        $blockId = 0;
        foreach ($allTokens as $token) {

            // if setup block is not started yet
            if ($blockSequence == 0 && $currentBlockName == 'setup' && count($blockTokens) == 0
                    && in_array($token[0], array(T_COMMENT, T_DOC_COMMENT, T_WHITESPACE))) {
                if ($token[0] == T_DOC_COMMENT) {
                    $tDocComments .= $token[1];
                }
                continue;
            }

            if ($token[0] === $blockHeaderSequence[$blockSequence]) {
                $blockSequence++;
                if ($blockSequence == 1) {
                    $suggestedBlockName = preg_replace('/^_+/','', $token[1]);
                    $suggestedBlockName = preg_replace('/_+$/','', $suggestedBlockName);
                }
                if ($blockSequence >= count($blockHeaderSequence)) {
                    
//                    if(isset($blockTokens[$suggestedBlockName])) {
//                        throw new ParseException('Block ' . $suggestedBlockName . ' is already defined!');
//                    }

                    // expect alias
                    if ($suggestedBlockName == 'expect') {
                        $suggestedBlockName = 'then';
                    }

                    if(!in_array($suggestedBlockName, $this->allowedBlocks)) {
                        throw new ParseException('Unknown block found: ' . $suggestedBlockName . '');
                    }

                    $currentBlockName = $suggestedBlockName;

                    $blockSequence = 0;
                    $blockSequenceData = array();
                    if (count($blockTokens) != 0) {
                        $blockId++;
                    }
                }

                $blockSequenceData[] = $token;
                continue;
            } else {
                if (count($blockSequenceData)) {

                    if (!isset($blockTokens[$blockId])) {
                        $blockTokens[$blockId] = array('name' => $currentBlockName, 'tokens' => array());
                    }

                    foreach($blockSequenceData as $seqToken) {
                        $blockTokens[$blockId]['tokens'][] = $seqToken;

                    }
                }
                $blockSequence = 0;
                $blockSequenceData = array();
            }

            if (!isset($blockTokens[$blockId])) {
                $blockTokens[$blockId] = array('name' => $currentBlockName, 'tokens' => array());
            }

            $blockTokens[$blockId]['tokens'][] = $token;
        }

        $this->validateBlocksOrder($blockTokens);

        $blocks = $this->collectBlocksCode($blockTokens);


        return array($tDocComments, $blocks);
    }

    private function collectBlocksCode($blockTokens)
    {
        $blocks = array();
        foreach ($blockTokens as $block) {

            $code = '';
            foreach ($block['tokens'] as $token) {
                $code .= $token[1];
            }

            $block['code'] = trim($code);
            $blocks[] = $block;
        }
        return $blocks;
    }

    private function validateBlocksOrder($blocks)
    {
        $validOrder = array(
            '' => array('setup', 'when', 'then'),
            'setup' => array('when', 'then'),
            'when' => array('then'),
            'cleanup' => array('where'),
            'then' => array('when', 'where', 'cleanup'),
        );

        $thenCount = 0;
        $whenCount = 0;
        $lastBlock = '';
        // check block order
        foreach ($blocks as $block) {

            $blockName = $block['name'];

            if ('then' == $blockName) {
                $thenCount++;
            }
            if ('when' == $blockName) {
                $whenCount++;
            }

            $validBlocks = $validOrder[$lastBlock];
            if (!in_array($blockName, $validBlocks)) {
                throw new ParseException('Unexpected ' . $blockName . ' Expecting one of: ' . implode(',', $validOrder[$lastBlock]));
            }

            $lastBlock = $blockName;
        }

        if ($thenCount == 0) {
            throw new ParseException('Block "then:" is required.');
        }
        if ($thenCount < $whenCount) {
            throw new ParseException('Each when block should have it\'s own then block');
        }
    }

    public function setAllowedBlocks($allowedBlocks)
    {
        $this->allowedBlocks = $allowedBlocks;
    }

    public function getAllowedBlocks()
    {
        return $this->allowedBlocks;
    }
}
