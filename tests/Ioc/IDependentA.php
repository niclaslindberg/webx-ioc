<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 11:03 AM
 */

namespace WebX\Ioc;


interface IDependentA
{
    /**
     * @return IA
     */
    public function getA();
}