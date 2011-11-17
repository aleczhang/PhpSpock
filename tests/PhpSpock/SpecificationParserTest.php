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

/**
 * @group several-when
 */
class SpecificationParserTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \PhpSpock\SpecificationParser
     */
    protected $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->parser = new SpecificationParser();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function parseFullSpecification()
    {
        // add some noise here to add complexity to parsing
        function(){}; $spec = function() { /** this line should not be saved! */
            /**
             * @var $sym *Mock*
             * @var $foo
             * @var $result MyClass *Mock*
             */

            setup:
            $foo = '123';

            when:
            $foo .= $sym;

            then:
            $foo == $result;

            _when:
            $foo .= $sym;

            expect_:
            $foo == $result;

            cleanup:
            unset($foo);

            where:
            $sym | $result;
            '!' | '123!';
            '4' | '1234';
         /** this line also will be ignored */ }; $a = function(){}; // and here some noise

        $result = $this->parser->parse($spec);

        $this->assertInstanceOf('PhpSpock\Specification', $result);

        $this->assertEquals('/**
             * @var $sym *Mock*
             * @var $foo
             * @var $result MyClass *Mock*
             */

            setup:
            $foo = \'123\';

            when:
            $foo .= $sym;

            then:
            $foo == $result;

            _when:
            $foo .= $sym;

            expect_:
            $foo == $result;

            cleanup:
            unset($foo);

            where:
            $sym | $result;
            \'!\' | \'123!\';
            \'4\' | \'1234\';', $result->getRawBody());

        $blocks = $result->getRawBlocks();

        $this->assertEquals(7, count($blocks));

        $varDeclarations = $result->getVarDeclarations();
        $this->assertEquals(2, count($varDeclarations));
        $this->assertArrayHasKey('sym', $varDeclarations);
        $this->assertArrayHasKey('result', $varDeclarations);
        $this->assertEquals('', $varDeclarations['sym']);
        $this->assertEquals('MyClass', $varDeclarations['result']);

        $this->assertEquals('setup', $blocks[0]['name']);
        $this->assertEquals('$foo = \'123\';', $blocks[0]['code']);

        $this->assertEquals('when', $blocks[1]['name']);
        $this->assertEquals('$foo .= $sym;', $blocks[1]['code']);

        $this->assertEquals('then', $blocks[2]['name']);
        $this->assertEquals('$foo == $result;', $blocks[2]['code']);

        $this->assertEquals('when', $blocks[3]['name']);
        $this->assertEquals('$foo .= $sym;', $blocks[3]['code']);

        $this->assertEquals('then', $blocks[4]['name']);
        $this->assertEquals('$foo == $result;', $blocks[4]['code']);

        $this->assertEquals('cleanup', $blocks[5]['name']);
        $this->assertEquals('unset($foo);', $blocks[5]['code']);

        $this->assertEquals('where', $blocks[6]['name']);
        $this->assertEquals('$sym | $result;
            \'!\' | \'123!\';
            \'4\' | \'1234\';', $blocks[6]['code']);
        

        $this->assertInstanceOf('PhpSpock\Specification\SetupBlock', $result->getSetupBlock());

        $whenThenPairs = $result->getWhenThenPairs();
        $this->assertTrue(is_array($whenThenPairs));
        $this->assertEquals(2, count($whenThenPairs));

        $this->assertInstanceOf('PhpSpock\Specification\SimpleBlock', $whenThenPairs[0]->getWhenBlock());
        $this->assertInstanceOf('PhpSpock\Specification\ThenBlock', $whenThenPairs[0]->getThenBlock());

        $this->assertInstanceOf('PhpSpock\Specification\SimpleBlock', $whenThenPairs[1]->getWhenBlock());
        $this->assertInstanceOf('PhpSpock\Specification\ThenBlock', $whenThenPairs[1]->getThenBlock());

        $this->assertInstanceOf('PhpSpock\Specification\SimpleBlock', $result->getCleanupBlock());

        $this->assertInstanceOf('PhpSpock\Specification\WhereBlock', $result->getWhereBlock());
    }

    /**
     * @test
     */
    public function parseSpecWithoutSetup()
    {
        $spec = function() {
            /**
             * @var $sym
             * @var $result
             */

            $foo = '123';

            when:
            $foo .= $sym;

            then:
            true;
         };

        $result = $this->parser->parse($spec);

        $this->assertInstanceOf('PhpSpock\Specification', $result);

        $this->assertEquals('/**
             * @var $sym
             * @var $result
             */

            $foo = \'123\';

            when:
            $foo .= $sym;

            then:
            true;', $result->getRawBody());

        $blocks = $result->getRawBlocks();
        $this->assertEquals(3, count($blocks));

        $this->assertEquals('setup', $blocks[0]['name']);
        $this->assertEquals('$foo = \'123\';', $blocks[0]['code']);
    }

    /**
     * @test
     * @expectedException PhpSpock\ParseException
     */
    public function parseSpecWithCodeOutsideOfSetup()
    {
        $spec = function() {
            /**
             * @var $sym
             * @var $result
             */

            $foo = '124';

            setup:
            $foo = '123';

            when:
            $foo .= $sym;
         };

        $result = $this->parser->parse($spec);

        $this->assertInstanceOf('PhpSpock\Specification', $result);

        $this->assertEquals('/**
             * @var $sym
             * @var $result
             */

            $foo = \'123\';

            when:
            $foo .= $sym;', $result->getRawBody());

        $blocks = $result->getRawBlocks();
        $this->assertEquals(2, count($blocks));

        $this->assertEquals('$foo = \'123\';', $blocks['setup']);
    }

    /**
     * @test
     * @expectedException PhpSpock\ParseException
     */
    public function badBlockOrder1()
    {
        $spec = function() {
            /**
             * @var $sym
             * @var $result
             * @var $foo
             */

            where:
            $foo .= $sym;

            then:
            $foo == $sym;
         };

        $result = $this->parser->parse($spec);
    }

    /**
     * @test
     */
    public function parseClassName()
    {
        $spec = function() {
            /**
             * @var $a
             */

            setup:
            $a = new PhpSpock();

            then:
            'PhpSpock' == get_class($a);
         };

        $result = $this->parser->parse($spec);
        $this->assertInstanceOf('PhpSpock\Specification', $result);

        $this->assertEquals('/**
             * @var $a
             */

            setup:
            $a = new PhpSpock();

            then:
            \'PhpSpock\' == get_class($a);', $result->getRawBody());

        $blocks = $result->getRawBlocks();
        $this->assertEquals(2, count($blocks));

        $this->assertEquals('setup', $blocks[0]['name']);
        $this->assertEquals('$a = new PhpSpock();', $blocks[0]['code']);
    }

    /**
     * @test
     */
    public function parseSpeckWitOneLineComments()
    {
        $spec = function() {
            /**
             * @var $sym
             * @var $result
             * @var $foo
             */

            // hofdasoihofsa
            // fasdas

            setup:
            $a = 1;

            when:
            $foo .= $sym;

            then:
            $foo == $sym;

         };

        $result = $this->parser->parse($spec);
    }

    /**
     * @test
     * @expectedException PhpSpock\ParseException
     */
    public function parseEmptyTest()
    {
        $spec = function() {
            
         };

        $result = $this->parser->parse($spec);
    }



    /**
     * @test
     * @expectedException PhpSpock\ParseException
     */
    public function parseSpecWithUnknownBlock()
    {
        $spec = function() {
            /**
             * @var $sym
             * @var $result
             */

            setup:
            $foo = '123';

            trololo:
            $foo .= $sym;

            when:
            $foo .= $sym;
         };

        $result = $this->parser->parse($spec);
    }
}
