<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 2:01 AM
 */

namespace WebX\Ioc;


class IocException extends \Exception {
    public function __construct($message, \Exception $cause=null) {
        parent::__construct($message,0,$cause);
    }
}