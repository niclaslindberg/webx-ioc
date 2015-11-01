<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 1:54 PM
 */

namespace WebX\Ioc;


Trait ToStringTrait {


    public function __toString() {
        return "Instance:" . get_class($this);
    }
}