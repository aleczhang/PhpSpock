<?php
/**
 * Date: 11/3/11
 * Time: 1:29 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
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
                $test == 123;';

        $result = $this->parser->parse($code);

        $this->assertType('PhpSpock\Specification\ThenBlock', $result);

        $expressions = $result->getExpressions();

        $this->assertEquals(2, count($expressions));

        $this->assertEquals('1 * $service->call()', $expressions[0]);
        $this->assertEquals('$test == 123', $expressions[1]);
    }
}
