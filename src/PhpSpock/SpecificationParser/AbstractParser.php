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
 * Time: 1:31 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock\SpecificationParser;
 
abstract class AbstractParser {

    protected function tokenizeCode($body)
    {
        $tokens = token_get_all('<?php ' . $body);

        foreach($tokens as $key => $token) {
            if (is_scalar($token)) {
                $tokens[$key] = array($token, $token);
            }
        }

        return $this->filtrateTokens($tokens);
    }

    protected function filtrateCode($code) {
        $newCode = '';
        $tokens = $this->tokenizeCode($code);
        foreach($tokens as $token) {
            switch ($token[0]) {
                case T_OPEN_TAG:
                case T_DOC_COMMENT:
                case T_COMMENT:
                    continue(2);

                default:
                    $newCode .= $token[1];
            }

        }
        return $newCode;
    }

    protected function filtrateTokens($tokens)
    {
        $filtredTokens = array();
        $hasContent = false;
        foreach ($tokens as $token) {
            switch ($token[0]) {
                case T_OPEN_TAG:
//                case T_DOC_COMMENT:
//                case T_COMMENT:
                    continue(2);

                case T_WHITESPACE:
                    if (!$hasContent) {
                        continue(2);
                    }

                default:
                    $filtredTokens[] = $token;
                    $hasContent = true;
            }
        }

        return $filtredTokens;
    }

    protected function printTokens($tokens)
    {
        foreach($tokens as $token) {
            $this->printToken($token);
        }
    }

    protected function printToken($token)
    {
        if (is_scalar($token)) {
            var_dump($token);
        } else {
            var_dump(array(is_string($token[0]) ? $token[0] : token_name($token[0]), $token[1]));
        }
    }

    protected function splitCodeExpressions($code, $preserveBlankLines = true)
    {
        $code = trim($code);
        if ($preserveBlankLines) {
            $code = preg_replace('/\n\s*\n/', ';', $code);
        }

        if (substr($code, -1) == ';') {
            $code = substr($code, 0, -1);
        }

        $lines = explode(';', $code);
        foreach($lines as $key => $line) {
            $lines[$key] = trim($line);
        }

        return $lines;
    }
}
