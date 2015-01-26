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
 * Time: 1:39 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock\Specification;
 
class WhenThenPair {

    /**
     * @var WhenBlock
     */
    private $whenBlock;

    /**
     * @var ThenBlock
     */
    private $thenBlock;

    /**
     * @param \PhpSpock\Specification\ThenBlock $thenBlock
     */
    public function setThenBlock($thenBlock)
    {
        $this->thenBlock = $thenBlock;
    }

    /**
     * @return \PhpSpock\Specification\ThenBlock
     */
    public function getThenBlock()
    {
        return $this->thenBlock;
    }

    /**
     * @param \PhpSpock\Specification\WhenBlock $whenBlock
     */
    public function setWhenBlock($whenBlock)
    {
        $this->whenBlock = $whenBlock;
    }

    /**
     * @return \PhpSpock\Specification\WhenBlock
     */
    public function getWhenBlock()
    {
        return $this->whenBlock;
    }
}
