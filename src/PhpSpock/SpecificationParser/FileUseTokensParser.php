<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

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
    }

    private function next()
    {
        while ($token = array_shift($this->tokens)) {
            if (in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT))) {
                continue;
            }

            return $token;
        }
    }

    private function nextValue()
    {
        $token = $this->next();

        return is_array($token) ? $token[1] : $token;
    }
}
