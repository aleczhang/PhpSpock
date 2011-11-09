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
            $ret = isset($__specification_Exception) && $__specification_Exception instanceof \\'.$exceptionName.';
            $__specification_Exception = null;
            $__specification__expressionResult = $ret;
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
}
