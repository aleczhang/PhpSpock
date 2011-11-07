<?php
/**
 * Date: 11/7/11
 * Time: 1:45 PM
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
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
