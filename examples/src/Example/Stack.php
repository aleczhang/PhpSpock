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
 * Date: 11/8/11
 * Time: 12:45 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace Example;
use \Example\Stack\EmptyStackException;
 
class Stack {

    private $data = array();

    public function size()
    {
        return sizeof($this->data);
    }

    public function pop()
    {
        if (sizeof($this->data) == 0) {
            throw new EmptyStackException;
        }
        return array_pop($this->data);
    }

    public function peek()
    {
        if (sizeof($this->data) == 0) {
            throw new EmptyStackException;
        }
        return $this->data[count($this->data) - 1];
    }

    public function push($el)
    {
        array_push($this->data, $el);
    }
}
