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
 * Time: 1:29 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock\SpecificationParser;
 
class WhereBlockParserTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \PhpSpock\SpecificationParser\WhereBlockParser
     */
    protected $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->parser = new WhereBlockParser();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function parseArrayNotation()
    {
        $code = '$myVar << array(1,2,3);';

        $result = $this->parser->parse($code);

        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock', $result);

        $params = $result->getParametrizations();
        $this->assertEquals(1, count($params));
        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[0]);

        $this->assertEquals('$myVar', $params[0]->getLeftExpression());
        $this->assertEquals('array(1,2,3)', $params[0]->getRightExpression());
    }

    /**
     * @test
     */
    public function parseSeveralLinesOfArrayNotation()
    {
        $code = '$myVar << array(1,2,3);
                $myVar2 << array(1,43,3);
                $myVar3 << array(1,2,$hoho);';

        $result = $this->parser->parse($code);

        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock', $result);

        $params = $result->getParametrizations();
        $this->assertEquals(3, count($params));
        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[0]);
        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[1]);
        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[2]);

        $this->assertEquals('$myVar', $params[0]->getLeftExpression());
        $this->assertEquals('array(1,2,3)', $params[0]->getRightExpression());

        $this->assertEquals('$myVar2', $params[1]->getLeftExpression());
        $this->assertEquals('array(1,43,3)', $params[1]->getRightExpression());

        $this->assertEquals('$myVar3', $params[2]->getLeftExpression());
        $this->assertEquals('array(1,2,$hoho)', $params[2]->getRightExpression());
    }

    /**
     * @test
     */
    public function parseTableNotation()
    {
        $code = '$sym | $result;
            \'!\' | \'123!\';
            \'!\' | $whatever;
            \'4\' | \'1234\';';

        $result = $this->parser->parse($code);

        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock', $result);

        $params = $result->getParametrizations();
        $this->assertEquals(2, count($params));
        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[0]);
        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[1]);

        $this->assertEquals('$sym', $params[0]->getLeftExpression());
        $this->assertEquals('array(\'!\',\'!\',\'4\')', $params[0]->getRightExpression());

        $this->assertEquals('$result', $params[1]->getLeftExpression());
        $this->assertEquals('array(\'123!\',$whatever,\'1234\')', $params[1]->getRightExpression());
    }

    /**
     * @test
     * @expectedException PhpSpock\ParseException
     */
    public function parseTableWithWrongTable()
    {
        $code = '$sym | $result;
            \'!\' | \'123!\';
            \'!\' | $whatever| 123;
            \'4\' | \'1234\';';

        $result = $this->parser->parse($code);
    }

    /**
     * @test
     */
    public function parseMixedNotation()
    {
        $code = '$myVar << array(1,2,3);

                $sym | $result;
                \'!\' | \'123!\';
                \'!\' | $whatever;
                \'4\' | \'1234\';

                $myVar3 << array(1,2,$hoho);';

        $result = $this->parser->parse($code);

        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock', $result);

        $params = $result->getParametrizations();
        $this->assertEquals(4, count($params));

        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[0]);
        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[1]);
        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[2]);
        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock\Parameterization', $params[3]);

        $this->assertEquals('$myVar', $params[0]->getLeftExpression());
        $this->assertEquals('array(1,2,3)', $params[0]->getRightExpression());

        $this->assertEquals('$sym', $params[1]->getLeftExpression());
        $this->assertEquals('array(\'!\',\'!\',\'4\')', $params[1]->getRightExpression());

        $this->assertEquals('$result', $params[2]->getLeftExpression());
        $this->assertEquals('array(\'123!\',$whatever,\'1234\')', $params[2]->getRightExpression());

        $this->assertEquals('$myVar3', $params[3]->getLeftExpression());
        $this->assertEquals('array(1,2,$hoho)', $params[3]->getRightExpression());
    }
}
