<?php
/**
 * Date: 11/3/11
 * Time: 1:29 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace PhpSpock\SpecificationParser;
 
class SimpleBlockParserTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \PhpSpock\SpecificationParser\ThenBlockParser
     */
    protected $parser;

    protected function setUp()
    {
        parent::setUp();

        $this->parser = new SimpleBlockParser();
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

        $this->assertType('PhpSpock\Specification\SimpleBlock', $result);

        $this->assertEquals($code, $result->getCode());
    }
}
