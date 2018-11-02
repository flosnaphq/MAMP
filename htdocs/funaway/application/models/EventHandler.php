<?php

class EventHandler {

    private static $events = array();

    private function __construct() {
        
    }

    private function __clone() {
        
    }

   
    
    public static function subscribe($name, $callback) {
        // Make sure the subscription isn't null
        if (empty(self::$events[$name]))
            self::$events[$name] = array();
        // push the $callback onto the subscription stack
        array_push(self::$events[$name], $callback);
    }

    public static function publish($name, $params = '') {
        // Check to see if the subscribe isn't null
        if (empty(self::$events[$name]))
            return false;
        // Gets all parameters passed to the function
        // and removes the first, which is $name
        $params = func_get_args();
        array_shift($params);
        // If there's only one event, then call it and return the value
        if (count(self::$events[$name]) === 1) {
            if (is_callable(self::$events[$name][0]))
                return call_user_func_array(self::$events[$name][0], $params);
            else
                return false;
        }
        // Loop through all the events and call them
        foreach (self::$events[$name] as $event) {
            if (is_callable($event))
                call_user_func_array($event, $params);
        }
    }

    public static function unsubscribe($name) {
        if (!empty(self::$events[$name]))
            unset(self::$events[$name]);
    }

}
