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
 
class ThenBlockParserTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \PhpSpock\SpecificationParser\ThenBlockParser
     */
    protected $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->parser = new ThenBlockParser();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function parseSeveralLines()
    {
        $code = '1 * $service->call();
                $test == 123; // hoho';

        $result = $this->parser->parse($code);

        $this->assertInstanceOf('PhpSpock\Specification\ThenBlock', $result);

        $expressions = $result->getExpressions();

        $this->assertEquals(2, count($expressions));

        $this->assertInstanceOf('PhpSpock\Specification\ThenBlock\Expression', $expressions[0]);
        $this->assertEquals('1 * $service->call()', $expressions[0]->getCode());

        $this->assertInstanceOf('PhpSpock\Specification\ThenBlock\Expression', $expressions[1]);
        $this->assertEquals('$test == 123', $expressions[1]->getCode());
        $this->assertEquals('hoho', $expressions[1]->getComment());
    }
}
