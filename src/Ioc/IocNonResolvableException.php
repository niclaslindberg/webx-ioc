<?php
/**
 * User: niclas
 * Date: 11/1/15
 * Time: 2:01 AM
 */

namespace WebX\Ioc;

/**
 * Thrown when a depdendency could not be resolved.
 * @package WebX\Ioc
 */
class IocNonResolvableException extends IocException {

    private $id;

    private $interfaceName;

    public function __construct($interfaceName, $id=null) {
        parent::__construct(sprintf("Could not resolve '%s' (id=%s)",$interfaceName,$id));
        $this->interfaceName = $interfaceName;
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function interfaceName()
    {
        return $this->interfaceName;
    }


}