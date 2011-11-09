
# PhpSpock

PhpSpock is a php implementation of Spock testing framework. Syntax of tests is replicated as much
as php language syntax permits. PhpSpock is standalone library, but is designed to be used in
partnership with other testings framework like PhpUnit.

Useful links:

* [Github page](https://github.com/ribozz/PhpSpock)
* [Spock framework](http://code.google.com/p/spock/)

## Installation

For now, only PhpUnit framework is supported out of the box.

### PhpUnit integration

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

### Implementing own test framework adapter

You can take a look at PhpUnitAdapter and how it is integrated into PhpSpock classes. PhpSpock is
designed in a way that allows easily integrate it in third party libraries.
If you conquer any situation when you need some extra-functionality (event, some extra interface method, etc.), feel
free to fork repository on github, and make pull request to merge your changes into main branch. But keep in mind
that PhpSpock core should remain unaware about any kind of testing framework and iteract with them using event system.

## Dictionary

* Specification - test that is written in Specification style.

## Writing specification

To run test as a specification, you should mark it with annotation @spec or give it a name, that is ending with "Spec":

    /**
     * @return void
     * @test
     * @spec
     */
    public function thisIsMySpecificationStyleTest()
    {
        ...
    }

    public function thisIsAlsoSpec()
    {
        ...
    }

NB! Spec should be also a valid phpUnit test when using phpUnit adapter.

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

* Define interactions in then block
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