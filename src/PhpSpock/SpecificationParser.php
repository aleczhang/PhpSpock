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
 * Time: 10:38 AM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock;

use PhpSpock\SpecificationParser\AbstractParser;

use \PhpSpock\SpecificationParser\SimpleBlockParser;
use \PhpSpock\SpecificationParser\WhereBlockParser;
use \PhpSpock\SpecificationParser\ThenBlockParser;
 
class SpecificationParser extends AbstractParser {

    protected $allowedBlocks = array('setup', 'when', 'then', 'where');

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
            $blocks = $this->splitOnBlocks($body);

        } catch(\Exception $e) {
            $newEx = new ParseException('Can not parse function defined in file ' . $fileName .' on line '. $lineStart
                . "\n". get_class($e) .': ' . $e->getMessage(), 0, $e);

            throw $newEx;
        }

        $parser = new \PhpSpock\SpecificationParser\FileUseTokensParser();
        list($namespace, $useStatements) = $parser->parseFile($fileName);

        $spec = new Specification();
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

    public function parseBlocks($blocks, $spec)
    {
        foreach ($blocks as $blockName => $blockCode) {

            switch ($blockName) {

                case 'setup':
                    $parser = new SimpleBlockParser();
                    $spec->setSetupBlock($parser->parse($blockCode));
                    break;

                case 'when':
                    $parser = new SimpleBlockParser();
                    $spec->setWhenBlock($parser->parse($blockCode));
                    break;

                case 'then':
                    $parser = new ThenBlockParser();
                    $spec->setThenBlock($parser->parse($blockCode));
                    break;

                case 'where':
                    $parser = new WhereBlockParser();
                    $spec->setWhereBlock($parser->parse($blockCode));
                    break;
            }
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


        foreach ($allTokens as $token) {

            if ($token[0] === $blockHeaderSequence[$blockSequence]) {
                $blockSequence++;
                if ($blockSequence == 1) {
                    $suggestedBlockName = $token[1];
                }
                if ($blockSequence >= count($blockHeaderSequence)) {
                    $blockSequence = 0;

                    if(isset($blockTokens[$suggestedBlockName])) {
                        throw new ParseException('Block ' . $suggestedBlockName . ' is already defined!');
                    }

                    if(!in_array($suggestedBlockName, $this->allowedBlocks)) {
                        throw new ParseException('Unknown block found: ' . $suggestedBlockName . '');
                    }

                    $currentBlockName = $suggestedBlockName;

                    $blockSequence = 0;
                    $blockSequenceData = array();
                }

                $blockSequenceData[] = $token;
                continue;
            } else {
                if (count($blockSequenceData)) {
                    foreach($blockSequenceData as $seqToken) {
                        $blockTokens[$currentBlockName][] = $seqToken;
                    }
                }
                $blockSequence = 0;
                $blockSequenceData = array();
            }

            if (!isset($blockTokens[$currentBlockName])) {
                $blockTokens[$currentBlockName] = array();
            }
            $blockTokens[$currentBlockName][] = $token;
        }

        $blocks = array();
        foreach($blockTokens as $blockName => $tokens) {

            $code = '';

            foreach($tokens as $token) {
                $code .= $token[1];
            }

            $blocks[$blockName] = trim($code);
        }

        $validOrder = $this->allowedBlocks;

        // check block order
        foreach(array_keys($blocks) as $blockName) {
            if (!in_array($blockName, $validOrder)) {
                throw new ParseException($blockName . ' is in wrong position');
            }
            $pos = array_search($blockName, $validOrder);
            $validOrder = array_slice($validOrder, $pos + 1);
        }

        if (!isset($blocks['then'])) {
            throw new ParseException('Block "then:" is required.');
        }

        return $blocks;
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
