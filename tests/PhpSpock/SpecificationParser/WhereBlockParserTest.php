<?php
/**
 * Date: 11/3/11
 * Time: 1:29 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
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

        $this->assertType('PhpSpock\Specification\WhereBlock', $result);

        $params = $result->getParametrizations();
        $this->assertEquals(1, count($params));
        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[0]);

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

        $this->assertType('PhpSpock\Specification\WhereBlock', $result);

        $params = $result->getParametrizations();
        $this->assertEquals(3, count($params));
        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[0]);
        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[1]);
        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[2]);

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

        $this->assertType('PhpSpock\Specification\WhereBlock', $result);

        $params = $result->getParametrizations();
        $this->assertEquals(2, count($params));
        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[0]);
        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[1]);

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

        $this->assertType('PhpSpock\Specification\WhereBlock', $result);

        $params = $result->getParametrizations();
        $this->assertEquals(4, count($params));

        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[0]);
        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[1]);
        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[2]);
        $this->assertType('PhpSpock\Specification\WhereBlock\Parameterization', $params[3]);

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
