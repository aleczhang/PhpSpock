
# PhpSpock

PhpSpock is a php implementation of Spock testing framework. Syntax of tests is replicated as much
as php language syntax permits. PhpSpock is standalone library, but is designed to be used in
partnership with other testings framework like PhpUnit.

Useful links:

* [Github page](https://github.com/ribozz/PhpSpock)
* [Spock framework](http://code.google.com/p/spock/)

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

    protected function runTest()
    {
        if (\PhpSpock\Adapter\PhpUnitAdapter::isSpecification($this)) {
            return \PhpSpock\Adapter\PhpUnitAdapter::runTest($this);
        } else {
            return parent::runTest();
        }
    }

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

## Writing specification

### Preparations

You can add specification taste to any PhpUnit test case (or some other framework if there is appropriate adapter).
All you need is to override runTest() method in your test case:

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

This way requires you to override runTest() method in each test class you create, but allows not to depend on extending
some particular TestCase implementation.

The other way is to put this method in to your common test case.

### Turn test case method into specification

When preparations are done You can write your first specification.

To run test as a specification, you should mark it with annotation @spec or give it a name, that is ending with "Spec":

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

NB! @spec annotation is not a replacement for @test, so you still should add @test annotation to your test case or
to start method name with "test" prefix.

### Specification syntax

Specification is a valid php code, so your IDE will not complain about bad syntax and even more it will give you
nice autocomplete for all code you write in your specification.

Specification consits of blocks:

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

Each block starts with block label (name of block followed by ':') and followed by a arbitary number of lines of code.

NB! You can not use labels and goto operator in your specification code. Or specification parser will complain you about bad syntax.

The only required block is "then", it also have alias "expect".

So, the minimal specification will look like:

    /**
     * @spec
     * @test
     */
    public function makeSureIAmStillGoodInMathematics()
    {
        then:
        2 + 2 == 4;
    }

Or even better:

    /**
     * @spec
     * @test
     */
    public function makeSureIAmStillGoodInMathematicsWithExcept()
    {
        expect:
        2 + 2 == 4;
    }

### "then" block

Then blcok is a set of expresions that may be just a piece of code or assertion.
Expressions are separated by ';' char.

Assertion is a piece of code that returns a boolean value.

NB! It's important that assertion should return exactly boolean result to be assertion.

Examples:

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

The one interesting thing in assertion, is that comment located on the same string with assertion will
bee added to exception message. Output for the last assertion in example will be:

    There was 1 failure:

    1) DocExamples\SpecificationSyntaxTest::assertionExamples
    Expression (bool) (2-2) is evaluated to false.

    assertion - expression is converted to boolean false, throwing an assertion exception


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

## Examples

One example is in folder "examples". Other examples will be later.
And something you can see also from PhpSpock tests. Especially take a look at
PhpSpockTest and SpecificationParserTest.

To execute examples, just run "phpunit" command in PhpSpock folder.

## Implemented features

* Spock syntax
* Support for "use" class import in tests
* PhpUnit framework adapter
* Parametrization
* Several when->then block pairs
* Custom error message in assertion
* Support for run under debugger
* Spock style mocking (Iteractions)

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