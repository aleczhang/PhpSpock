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
 * Date: 11/7/11
 * Time: 9:04 AM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
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
        return $this->stripComments($this->code);
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function compile()
    {
        $code = $this->code;
        $code = $this->stripComments($code);

        if (preg_match('/^\s*thrown\((("|\')([\\\a-zA-Z0-9_]+)("|\'))?\)\s*$/', $code, $mts)) {

            $exceptionName = isset($mts[3]) ? $mts[3] : 'Exception';

            $code = '
            $__specification_ret = isset($__specification_Exception) && $__specification_Exception instanceof \\'.$exceptionName.';
            $__specification_Exception = null;
            $__specification__expressionResult = $__specification_ret;
            ';

            return $code;
        }

        if (preg_match('/^\s*notThrown\((("|\')([\\\a-zA-Z0-9_]+)("|\'))?\)\s*$/', $code, $mts)) {

            $exceptionName = isset($mts[3]) ? $mts[3] : 'Exception';

            $code = '
            $ret = !isset($__specification_Exception) ||  !($__specification_Exception instanceof \\'.$exceptionName.');
            ';

            if ($exceptionName == 'Exception') {
                $code .= '
                $__specification_Exception = null;';
            }

            $code .= '
            $__specification__expressionResult = $ret;';

            return $code;
        }

        if (trim($code) == '') {
            return null;
        }

        $code = '
        if (isset($__specification_Exception) && $__specification_Exception instanceof \Exception) {
            throw $__specification_Exception;
        }
        $__specification__expressionResult = '.$code.';';

        return $code;
    }

    private function stripComments($code)
    {
        $newCode = '';
        foreach(token_get_all('<?php ' .$code) as $token) {
            if (is_array($token) && ($token[0] == T_OPEN_TAG || $token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT)) {
                continue;
            }
            $newCode .= is_array($token) ? $token[1] : $token;
        }
        return trim($newCode);
    }

//    public function splitCode()
//    {
//        $code = $this->getCode();
//        $parts = preg_split('/\s+/', $code);
//
//        $pattern = array();
//        $data = array();
//        foreach($parts as $index => $part) {
//
//            if (preg_match('/[a-zA-Z]+/', $part)) {
//                $partName = 'exp' . ($index + 1);
//                if (strpos($part, '->') !== false) {
//                    $data[$partName] = explode('->', $part);
//                } else {
//                    $data[$partName] = $part;
//                }
//                $pattern[] = '$' . $partName;
//            } else {
//                $pattern[] = $part;
//            }
//        }
//        return array(
//            'parts' => $data,
//            'pattern' => implode(' ', $pattern)
//        );
//    }
}
