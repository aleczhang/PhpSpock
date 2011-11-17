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
 * Date: 11/7/11
 * Time: 1:45 PM
 * @author Aleksandr Rudakov <ribozz@gmail.com>
 */

namespace PhpSpock;

 
class Event extends \Symfony\Component\EventDispatcher\Event {

    /**
     * Process unit test code before execution
     */
    const EVENT_BEFORE_CODE_GENERATION = 'event_before_code_generation';

    /**
     * Collect extra variables that will be injected into test body.
     * All attributes will be exported as extra variables.
     */
    const EVENT_COLLECT_EXTRA_VARIABLES = 'event_collect_variables';

    /**
     * Executed, when exception is thrown from unit test.
     * Exception could be converted by replacing 'exception' attribute
     * in event.
     */
    const EVENT_TRANSFORM_TEST_EXCEPTION = 'event_transform_exception';

    const EVENT_DEBUG = 'event_debug';

    const EVENT_MODIFY_ASSERTION_COUNT = 'event_modify_assertion_count';

    

    private $attributes = array();

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function getAllAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        return null;
    }
}
