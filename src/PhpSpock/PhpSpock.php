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
 * Time: 5:21 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock;

 
class PhpSpock {

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    

    public function run($test) {
        $parser = new SpecificationParser();

        $testSpec = $parser->parse($test);

        $testSpec->setEventDispatcher($this->getEventDispatcher());

        try {
            return $testSpec->run();
        } catch(TestExecutionException $e) {
            $newEx = new TestExecutionException('Test failed: ' . $testSpec->getFile() .' on line '. $testSpec->getStartLine()
                . "\n". get_class($e) .': ' . $e->getMessage(), 0, $e);

            throw $newEx;
        }
    }

    /**
     * @param Adapter $adapter
     * @param $test
     * @return int
     */
    public function runWithAdapter(Adapter $adapter, $test)
    {
        return $adapter->run($test, $this);
    }

    public function runWithPhpUnit($test)
    {
        return $this->runWithAdapter( new \PhpSpock\Adapter\PhpUnitAdapter(), $test);
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        if (!$this->eventDispatcher) {
            $this->eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();
        }
        return $this->eventDispatcher;
    }
}
