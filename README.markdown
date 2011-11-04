
# PhpSpock

PhpSpock is a php implementation of Spock testing framework. Syntax of tests is replicated as much
as php language syntax permits. PhpSpock is standalone library, but is designed to be used in
partnership with other testings framework like PhpUnit.

Useful links:

* [Github page](https://github.com/ribozz/PhpSpock)
* [Spock framework](http://code.google.com/p/spock/)

## Installation

For now, only PhpUnit framework is supported

Just override runTest method in your TestCase or right in test:

    protected function runTest()
    {
        if (\PhpSpock\Adapter\PhpUnitAdapter::isSpecification($this)) {
            return \PhpSpock\Adapter\PhpUnitAdapter::runTest($this);
        } else {
            return parent::runTest();
        }
    }

## Examples

One example is in folder "examples". Other examples will be later.
And something you can see also from PhpSpock tests. Especially take a look at
PhpSpockTest and SpecificationParserTest.

To execute examples, just run "phpunit" command in PhpSpock folder.

## Plans

Features to implement:

* Several when->then block pairs
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