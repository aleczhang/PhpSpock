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
namespace PhpSpock\SpecificationParser;

class FileUseTokensParser
{
    private $tokens;

    public function parseFile($filename)
    {
        $src = file_get_contents($filename);

        $this->tokens = token_get_all($src);
        $uses = array();

        $namespace = '';
        while ($token = $this->next()) {
            if (T_NAMESPACE === $token[0]) {
                $namespace = $this->parseNamespace();
            } elseif (T_USE === $token[0]) {
                foreach ($this->parseUseStatement() as $useStatement) {
                    list($alias, $class) = $useStatement;
                    $uses[$alias] = $class;
                }
            }
        }

        return array($namespace, $uses);
    }

    private function parseNamespace()
    {
        $namespace = '';
        while ($token = $this->next()) {
            if (T_NS_SEPARATOR === $token[0] || T_STRING === $token[0]) {
                $namespace .= $token[1];
            } elseif (is_string($token) && in_array($token, array(';', '{'))) {
                return $namespace;
            }
        }
        return $namespace;
    }

    private function parseUseStatement()
    {
        $statements = $class = array();
        $alias = '';
        while ($token = $this->next()) {
            if (T_NS_SEPARATOR === $token[0] || T_STRING === $token[0]) {
                $class[] = $token[1];
            } else if (T_AS === $token[0]) {
                $alias = $this->nextValue();
            } else if (is_string($token)) {
                if (',' === $token || ';' === $token) {
                    $statements[] = array(
                        $alias ? $alias : $class[count($class) - 1],
                        implode('', $class)
                    );
                }

                if (';' === $token) {
                    return $statements;
                }
                if (',' === $token) {
                    $class = array();
                    $alias = '';

                    continue;
                }
            }
        }

        return $statements;
    }

    /**
     * @return mixed
     */
    private function next()
    {
        while ($token = array_shift($this->tokens)) {
            if (in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT))) {
                continue;
            }

            return $token;
        }
        return null;
    }

    private function nextValue()
    {
        $token = $this->next();

        return is_array($token) ? $token[1] : $token;
    }
}
