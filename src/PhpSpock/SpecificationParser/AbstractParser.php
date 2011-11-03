<?php
/**
 * Date: 11/3/11
 * Time: 1:31 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
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

    protected function filtrateTokens($tokens)
    {
        $filtredTokens = array();
        $hasContent = false;
        foreach ($tokens as $token) {
            switch ($token[0]) {
                case T_OPEN_TAG:
                case T_DOC_COMMENT:
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
