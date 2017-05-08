<?php

namespace WebX\Ioc;
use Exception;

/**
 * Exception for errors in the IOC container.
 * Class IocException
 * @package WebX\Ioc
 */
class IocException extends Exception {
    public function __construct($message, Exception $cause=null) {
        parent::__construct($message,0,$cause);
    }
}