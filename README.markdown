
# PhpSpock

PhpSpock is a php implementation of Spock testing framework. Syntax of tests is replicated as much
as php language syntax permits. PhpSpock is standalone library, but is designed to be used in
partnership with other testings framework like PhpUnit.

Useful links:

* [Github page](https://github.com/ribozz/PhpSpock)
* [Spock framework](http://code.google.com/p/spock/)

## Implemented features

* Spock syntax
* Support for "use" class import in tests
* PhpUnit framework adapter
* Parametrization
* Several when->then block pairs
* Custom error message in assertion
* Support for run under debugger
* Spock style mocking (Iteractions)

## Changelog

### 0.1.1

- Several bugfixes

### 0.1.2

- Simplified cardinality syntax. Now you can ommit "(" and ")" and use +1, -1, +0 instead of constructions like (_..4)

## Known problems

### Problem with @specDebug

**Description:**
When you generate debug code with @specDebug, some errors are thrown into console. The same thing when you delete
this annotation.

**Reason:**
PhpUnitAdapter changes code of class where the marked test method resist (to insert debug code). And now when
specification parser tries to get body of some other test in this file using reflection, it fails, beacuse
reflection does not reflect file changes.

**Solution:**
If you need to add/remove @specDebug annotation, just execute phpunit command twice gnoring all errors appeared.
Debug code still will be valid and should run correctly on a second time.


## Plans

Features to implement:

* Make assertionFailure output more descriptive
* Create proper docs with index on github pages

# Licence

Full text of licenses are attached as COPYING and COPYING.LESSER files.

    PhpSpock is free software: you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    PhpSpock is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with PhpSpock.  If not, see <http://www.gnu.org/licenses/>.

Copyright 2011 Aleksandr Rudakov <ribozz@gmail.com>

## Installation

For a moment the only way to install PhpSpock is to checkout sourcecode from git and to feet it to
some PSR0 compatible autoloader.

You can checkout source code from github: git://github.com/ribozz/PhpSpock.git

Current stable release is: 0.1

So, something like:

git clone git://github.com/ribozz/PhpSpock.git
git checkout 0.1

### Integration with frameworks

For now, only PhpUnit framework is supported out of the box.

#### PhpUnit integration

Integration with phpUnit is optional. The only thing it gives, is that you wouldn'r need anymore to override
runTest() method in every your test case where you use specification.

Just override runTest method in your common TestCase or right in phpUnit test:

```php
<?php

    protected function runTest()
    {
        if (\PhpSpock\Adapter\PhpUnitAdapter::isSpecification($this)) {
            return \PhpSpock\Adapter\PhpUnitAdapter::runTest($this);
        } else {
            return parent::runTest();
        }
    }
```

#### Implementing own test framework adapter

You can take a look at PhpUnitAdapter and how it is integrated into PhpSpock classes. PhpSpock is
designed in a way that allows easily integrate it in third party libraries.
If you conquer any situation when you need some extra-functionality (event, some extra interface method, etc.), feel
free to fork repository on github, and make pull request to merge your changes into main branch. But keep in mind
that PhpSpock core should remain unaware about any kind of testing framework and iteract with them using event system.


## User guide

### Intro

As you already know PhpSpock is a clone of Spock testing framework, so you can read also SpockBasics document
 to get more about ideas laying in the basement of both frameworks: [http://code.google.com/p/spock/wiki/SpockBasics].

## Terminology

"Spock lets you write specifications that describe expected features (properties, aspects) exhibited by a system of
interest. The system of interest could be anything between a single class and a whole application, and is also called
system under specification (SUS). The description of a feature starts from a specific snapshot of the SUS and its
collaborators; this snapshot is called the feature's fixture." (c) *SpockBasics*.

In this tutorial I am aplying term "specification" to the feature method. Because feature method is actually, a specification
of a feature. This assumption differs from terminology of Speck framework.

## Writing specification

### Preparations

You can add specification taste to any PhpUnit test case (or some other framework if there is appropriate adapter).
All you need is to override runTest() method in your test case:

```php
<?php

    namespace MyExamples;

    use \PhpSpock\Adapter\PhpUnitAdapter as PhpSpock;

    class WithoutIntegrationTest extends \PHPUnit_Framework_TestCase
    {
        protected function runTest()
        {
            if (\PhpSpock\Adapter\PhpUnitAdapter::isSpecification($this)) {
                return \PhpSpock\Adapter\PhpUnitAdapter::runTest($this);
            } else {
                return parent::runTest();
            }
        }

        ...
    }
```

This way requires you to override runTest() method in each test class you create, but allows not to depend on extending
some particular TestCase implementation.

The other way is to put this method in to your common test case.

### Turn test case method into specification

When preparations are done You can write your first specification.

To run test as a specification, you should mark it with annotation @spec or give it a name, that is ending with "Spec":

```php
<?php

    /**
     * @test
     * @spec
     */
    public function thisIsMySpecificationStyleTest()
    {
        ...
    }

    /**
     * @test
     */
    public function thisIsAlsoBecauseItEndsWithSpec()
    {
        ...
    }
```

NB! @spec annotation is not a replacement for @test, so you still should add @test annotation to your test case or
to start method name with "test" prefix.

### Specification syntax

Specification is a valid php code, so your IDE will not complain about bad syntax and even more it will give you
nice autocomplete for all code you write in your specification.

Specification consits of blocks:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function myTest()
    {
        setup:
        ...

        when:
        ...

        then:
        ...

        where:
        ...

        cleanup:
        ...
    }
```

Each block starts with block label (name of block followed by ':') and followed by a arbitary number of lines of code.

NB! You can not use labels and goto operator in your specification code. Or specification parser will complain you about bad syntax.

The only required block is "then", it also have alias "expect".

So, the minimal specification will look like:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function makeSureIAmStillGoodInMathematics()
    {
        then:
        2 + 2 == 4;
    }
```

Or even better:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function makeSureIAmStillGoodInMathematicsWithExcept()
    {
        expect:
        2 + 2 == 4;
    }
```

### "then" block

Then blcok is a set of expresions that may be just a piece of code or assertion.
Expressions are separated by ';' char.

Assertion is a piece of code that returns a boolean value.

NB! It's important that assertion should return exactly boolean result to be assertion.

Examples:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function assertionExamples()
    {
        expect:
        2 + 2 == 4;      // assertion - true, ignoring
        3 - 3;           // not an assertion - ignoring
        true;            // assertion - true, ignoring
        (bool) (2-2);    // assertion - expression is converted to boolean false, throwing an assertion exception
    }
```

The one interesting thing in assertion, is that comment located on the same string with assertion will
bee added to exception message. Output for the last assertion in example will be:

    There was 1 failure:

    1) DocExamples\SpecificationSyntaxTest::assertionExamples
    Expression (bool) (2-2) is evaluated to false.

    assertion - expression is converted to boolean false, throwing an assertion exception


### "when" block

Despite you can write into "then" block not only assertions, but usual code also (Let's name it "actions"),
still better place for actions is "when" block.

"Then" block is usually working in pair with "when" block. When block contains actions and "then" block
contains assertions of expected result:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function whenThenExample()
    {
        when:
        $a = 1 + 2;

        then:
        $a == 3;
    }
```

These block combination is called "when-then" pair. And you even can use several "when-then" pairs:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function whenThenExampleWithSeveralPairs()
    {
        when:
        $a = 1 + 2;

        then:
        $a == 3;

        when_:
        $a += 4;

        then_:
        $a == 7;
    }
```

But there is a php syntax restrictions we need to take into account: php does not allow several labels
with the same name in one class method, so we need to add underscore "_" to the end of block name.
You can add as much underscores to the and of block name as you need. Underscores will be just ignored.

More pairs:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function whenThenExampleWithMorePairs()
    {
        when:
        $a = 1 + 2;

        then:
        $a == 3;

        when_:
        $a += 4;

        then_:
        $a == 7;

        when__:
        $a -= 2;

        then__:
        $a == 5;
    }
```

### "setup" and "cleanup" blocks

"setup" block is a block that should contain initialization code for your test:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function setupBlock()
    {
        setup:
        $a = 3 + rand(2, 4);

        expect:
        $a > 3;
    }
```

You also can ommit "setup" block label, if you want:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function setupBlockWithoutLabel()
    {
        $a = 3 + rand(2, 4);

        expect:
        $a > 3;
    }
```

In this case parser will assume that setup block is all the code form starting of the method till
the first labeled block.

"cleanup" block is executed after your test is completed:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function setupBlockWithCleanup()
    {
        setup:
        $temp = tmpfile();

        when:
        fwrite($temp, "writing to tempfile");

        then:
        notThrown('Exception');

        when_:
        fseek($temp, 0);
        $data = fread($temp, 1024);

        then_:
        $data == "writing to tempfile";

        cleanup:
        fclose($temp); // this removes the file according to tmpfile() docs
    }
```

NB! Cleanup block will not be executed if your code throws unexpected exception/fatal error or just
contains some syntactical errors.

### "where" block

Where block is a special block that contains so called "Parametrizations" it is a way to execute one specifiaction
on different sets of data. It is very like phpUnit "data sets", but better because parametrizations can also
use variables defined in setup block.

NB! It's important to understand, that test with parametrization will be executed several times from top to bottom
including setup and cleanup blocks. Only data will be different between executions.

Parametrization has two syntaxes (or notations). One is array style:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function parametrizationArrayNotation()
    {
        /**
         * @var $a
         */

        expect:
        $a + 2 > 0;

        where:
        $a << array(1, 2, 3);
    }
```

Here you say that specification will be executed three times and $a will contain each value from array(1,2,3);

And same table style:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function parametrizationTableNotation()
    {
        /**
         * @var $a
         * @var $b
         * @var $c
         */

        expect:
        $a + $b == $c;

        where:
        $a  | $b  | $c;
         1  |  2  |  3;
         3  |  2  |  5;
         3  |  4  |  7;
        -3  |  4  |  1;
    }
```

This is better when you need to assign multiple variables.
Parser will transform this table into:

```php
<?php

         $a << array(1, 3, 3, -3);
         $b << array(2, 2, 4,  4);
         $c << array(3, 5, 7,  1);
```

Each table row should contain equal amount of columns with table header (first row) and there should be no empty lines between rows of one table.
Amount of spaces between values and separators is not important.

You can notice that two last test has doc-block comment with variable declarations:

```php
<?php

    /**
     * @var $a
     * @var $b
     * @var $c
     */
```

This tells your IDE that these variables will be dinamicly created, and IDE will not complain about undefined variable.
You can also add type to variable, and get nice autocomplete:

```php
<?php

    /**
     * @var $stack \Example\Stack
     * @var $b
     * @var $c
     */
```

You can combine Table and Array notation of parametrization in one test:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function parametrizationMixedNotation()
    {
        /**
         * @var $a
         * @var $b
         * @var $c
         * @var $d
         * @var $e
         * @var $f
         */

        expect:
        $a + $b + $c + $d + $e + $f > 0;

        where:
        $a  | $b  | $c;
         1  |  2  |  3;
         3  |  2  |  5;
         3  |  4  |  7;
        -3  |  4  |  1;

        $d << array(1, 2, 3);

        $e  | $f;
         2  |  3;
         2  |  5;
    }
```

And if some parametrization statemets have different amount of values, values will be rolled:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function parametrizationValueRolling()
    {
        /**
         * @var $a
         * @var $b
         */

        expect:
        $a + $b > 0;

        where:
        $a << array(1, 2, 3);
        $b << array(1, 2);
    }
```

Results in following combinations:

```php
<?php

    $a: 1, $b: 1
    $a: 2, $b: 2
    $a: 3, $b: 1
```

Cont of iterations will be the ammount of elements in biggest parametrization.

You can also use any variables defined in setup statement:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function parametrizationVariablesFromSetup()
    {
        /**
         * @var $a
         */
        setup:
        $b = 123;

        expect:
        $a + 1  > 100;

        where:
        $a << array($b + 1, $b + 3, 101);
    }

    /**
     * @spec
     * @test
     */
    public function parametrizationVariablesFromSetupInTable()
    {
        /**
         * @var $a
         * @var $c
         */
        setup:
        $b = 123;

        expect:
        $a + $c + 1  > 100;

        where:
        $a      | $c;
        $b + 1  | 1 ;
        2       | $b + 3;
        101     | 3;
    }
```

And even use some external method or variable as paramtrization value source:

```php
<?php

    /**
     * @spec
     * @test
     */
    public function parametrizationWithExternalValueSource()
    {
        /**
         * @var $word
         */
        setup:
        $myDataProvider = function() {
            return explode(' ', 'When in the Course of human events it becomes necessary for one people to dissolve the political bands which have connected them with another and to assume among the powers of the earth, the separate and equal station to which the Laws of Nature and of Nature\'s God entitle them, a decent respect to the opinions of mankind requires that they should declare the causes which impel them to the separation.');
        };

        expect:
        preg_match('/[a-zA-Z]{1,15}/', $word) == true;

        where:
        $word << $myDataProvider();
    }
```

Here we test that the given text contains only words with atleast one english char.

If there is an assertion error, current parametrization parameters will be also added to error message.
Let's change the test a bit to see how assertion error look like with parametrization params:

```php
<?php

    ...
    preg_match('/^[a-zA-Z]{1,15}$/', $word) == true;
    ...
```

Here is output:

    There was 1 failure:

    1) DocExamples\SpecificationSyntaxTest::parametrizationWithExternalValueSource
    Expression preg_match(\'/^[a-zA-Z]{1,15}$/\', $word) == true is evaluated to false.

     Where:
    ---------------------------------------------------
      $word :  'earth,'


     Parametriazation values [step 32]:
    ---------------------------------------------------
     $word :  element[32] of array: $myDataProvider()


     Declared variables:
    ---------------------------------------------------
     $myDataProvider  : instance of Closure
     $word            : earth,

    ---------------------------------------------------

We can clearly see that word 'earth,' contains coma on the end and it doies not pass the regexp.

By the way data provider may be also a public method of test class:

```php
<?php

        ...
        where:
        $word << $this->myDataProvider();
        ...
```
## Testing exception

You can test exception in several ways. The first one is the phpUnit way:

```php
<?php
    /**
     * @spec
     * @expectedException Exception
     */
    public function testIndex()
    {
        when:
        throw new \Exception("test");

        then:
        $this->fail("Exception should be thrown!");
    }
```

Better way is to use thrown() and notThrown() constructions:

```php
<?php

    /**
     * @spec
     */
    public function testIndexWithThrown()
    {
        when:
        throw new \Exception("test");

        then:
        thrown("Exception");
    }
```

For now I didn't found the way to tell the IDE that thrown() and notThrown() functions are exist. So, for a moment IDE (at least
phpStorm) reacts with warning "undefined function" on these methods.

thrown() accepts class name as argument, if you give no argument, 'Exception' is assumed by default.

thrown() will check if exception was thrown in "when" block and fails with assertion error if not:

```php
<?php

    /**
     * @spec
     */
    public function testIndexWithThrown3()
    {
        when:
        throw new \RuntimeException("test");

        then:
        thrown("RuntimeException");
    }
```

The output will be:

    There was 1 failure:

    1) MyExamples\ExceptionExampleTest::testIndexWithThrown3
    Expression thrown("RuntimeException") is evaluated to false.

notThrown() makes test more complete. In any case test will fail if exception occours, but purpose
of your test will be more clear, if you have notThrown() statement in your "then" block.

More over each specification should contain at least one "then" (or "expect") block and it must not be empty.

thrown() and notThrown() assertions are applyed only to the last "when" block. This allows to do things like:

```php
<?php

    /**
     * @spec
     */
    public function testExceptionCombination()
    {
        when:
        1==1;

        then:
        notThrown("RuntimeException");

        _when:
        throw new \RuntimeException("test");

        _then:
        thrown("RuntimeException");
    }

```

If you have exception occure in your setup block, it's logical that your test will blow up.


## Mocking and Inteactions

PhpSpock uses Mockery mock framework under the hood, but it's DSL is adopted to meet Spock style.

To create a mock object, you should create a dock block in the beggining of your specification,
and declare variable. In declaration, the first parametr should be a class or interface name and
the second is "*Mock*" keyword, that tells parser that variable should be mocked.

This @var style declaration of mocks is good, because IDE will give you an autocomplete for your
mock. This usually does not occour, when you create mocks with Mockery natively.

Her is an example:

```php
<?php

    /**
     * @spec
     */
    public function test1()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        1 * $a->add(_,_);

        when:
        $a->add(1,2);

        then:
        notThrown();
    }
```
Here we define a new mock of type \Example\Calc and declare in setup block, that method "add()" should
be called once with two arbitary parameters.

Construction "1 * $a->add(_,_);" is called iteraction, and may be inserted in setup block, or in then block.

In setup block you can declare test-wide iteractions, usually these are declarations of retrun values for
optional methods:

```php
<?php

    /**
     * @spec
     */
    public function testSetupBlockInteractions()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        (0.._) * $a->add(1, 2) >> 3;
        (0.._) * $a->add(2, 2) >> 4;

        when:
        $b = $a->add(1,2);

        then:
        $b == 3;
    }
```

Sure some other class will call your mock's methods, but for illustration what is happening, above piece of code is good.

And in then method you usually will usally declare your expectations about count of method calls on mock:

```php
<?php

    /**
     * @spec
     */
    public function test3()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */

        when:
        $b = $a->add(1,2);

        then:
        1 * $a->add(_,_) >> 4;
        $b == 4;
    }
```

Here is the syntax of iteraction declaration:

    {Cardinality} * ${mockVarName}->{mockedMethodName}([{argument declaration}]) [ >> {return value declaration}]

### Cardinality

Cardinality is exact number of calls expected like "1", "2" or 0, or intervals:

    (n.._) * subscriber.receive(event) // at least n times

    (_..n) * subscriber.receive(event) // at most n times

    (m..n) * subscriber.receive(event) // between m and n times

An alternative syntax for intervals:

    +n * subscriber.receive(event) // at least n times

    -n * subscriber.receive(event) // at most n times

    m..n * subscriber.receive(event) // between m and n times

For example:

    +0 * $a->add(_,_) >> throws('RuntimeException', 'foo');

### mockVarName and mockedMethodName

Just a strings.

### Argument declaration

Format is: arg1, arg2, .... argN

Special format is: _*_ which declares that method may be called with arbitary argument count.

Argument is:

 * a constant like: 1, 2, "some string", WHATEVER_CONTANT ... and so on. Compared with "=="
 * $variable name - will be checked by reference
 * "_" any value (isuseful for defining argument count: like _,_,_,_)
 * something([some params]) will be transformed to \Mockery::something(some params) refer to mockery docs [https://github.com/padraic/mockery]

### Return value

 * a constant
 * usingClosure(function(){}) - closure will get arguments the method has received
 * throw(ExceptionInstance) - method will throw an exception
 * a variable


### More examples

```php
<?php

    /**
     * @spec
     */
    public function test9()
    {
        /**
         * @var $a \Example\Calc *Mock*
         */
        setup:
        1 * $a->add(_,_) >> throws('RuntimeException', 'foo');

        when:
        $b = $a->add(1,2);

        then:
        thrown('RuntimeException');
    }
```

## Shared resources

NB! It is very important, that you declare all resources that you are going to use in test, as public.
Otherwise your specification will not be able to call these resources, because test will be executed
in different context.

## Debugger support with phpUnit

Sometimes it is useful to debug your test in interactive debugger. For example in your IDE with xdebug.

PhpSpoc specification usuualy generates test code and executes it usning eval. So in ususal way you can not
assign any breakpoints on a specification method.

In this case, if you are using phpUnitAdapter, just add @specDebug annotation (in addition to existing @spec) and PhpSpock will generate
native phpUnit testCase method next to your specification method. this method will be flooded with internal phpSpock stuff,
bu it will make the thing. Every time you run your tests this generated test will be executed, so you can assign breakpoints on this
code.

After you managed with your bugs and willing to get rid of this crappy generated code, just remove annotation @specDebug and phpSpec
will clean up your test for you.

Also @specDebug may be helpfull in understanding internals of phpSpock. For example, if you have some missunderstandable behavior
of your test and think that PhpSpock is working wrong.

