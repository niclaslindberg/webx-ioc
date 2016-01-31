<?php

namespace WebX\Ioc\Impl;


use WebX\Ioc\Ioc;
use WebX\Ioc\IocException;

class ProxyFactory {

    private $proxies = array();
    /**
     * @var Ioc
     */
    private $ioc;

    public function __construct(IOC $ioc) {
        $this->ioc = $ioc;
    }

    public function createProxy(\ReflectionClass $interfaceClass) {
        $proxyClassName = $interfaceClass->getShortName() . "Proxy";
        if ($existingProxy = isset($this->proxies[$proxyClassName]) ? $this->proxies[$proxyClassName] : null) {
            return $existingProxy;
        } else if ($interfaceClass->isInterface()) {
            if (!class_exists($proxyClassName)) {
                if ($interfaceClass->isInterface()) {
                    $classDefParts = [];
                    $classDefParts[] = sprintf('class %s implements \\%s {', $proxyClassName, $interfaceClass->getName());
                    $classDefParts[] = sprintf('private $ioc;');
                    $classDefParts[] = sprintf('private $realObject;');
                    $classDefParts[] = sprintf('public function __construct($ioc) {');
                    $classDefParts[] = sprintf('$this->ioc = $ioc;');
                    $classDefParts[] = sprintf('}');

                    foreach ($interfaceClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $interfaceMethod) {
                        $methodParts = [];
                        $methodParts[] = sprintf("public function %s(", $interfaceMethod->getShortName());
                        $methodParamDeclarations = [];
                        $methodParamNames = [];
                        foreach ($interfaceMethod->getParameters() as $imParam) {
                            $methodParamName = '$' . $imParam->getName();
                            $methodParamNames[] = $methodParamName;
                            if ($imParamClass = $imParam->getClass()) {
                                if ($imParamClass->isInterface()) {
                                    $methodParamDeclarations[] = sprintf("%s %s", $imParamClass->getName(), $methodParamName);
                                } else {
                                    throw new IocException("Static initilization does not allow classes");
                                }
                            } else {
                                $methodParamDeclarations[] = $methodParamName;
                            }
                        }
                        $methodParts[] = sprintf('%s) {', implode(",", $methodParamDeclarations));
                        $methodParts[] = sprintf('if (!$this->realObject) {');
                        $methodParts[] = sprintf('$this->realObject = $this->ioc->get(\'%s\');', $interfaceClass->getName());
                        $methodParts[] = sprintf('}');
                        $methodParts[] = sprintf('return $this->realObject->%s(%s);', $interfaceMethod->getName(), implode(",", $methodParamNames));
                        $methodParts[] = "}";
                        $classDefParts[] = implode("\n", $methodParts);
                    }
                    $classDefParts[] = "}";
                    $classDef = implode("\n", $classDefParts);
                    eval($classDef);
                } else {
                    throw new IocException("Can not create proxy for '{$interfaceClass}' Not an interface.");
                }
            }
            $proxy = new $proxyClassName($this->ioc);
            $this->proxies[$proxyClassName] = $proxy;
            return $proxy;
        } else {
            throw new IocException("Can not create proxy for '{$interfaceClass}' is not an interface.");
        }
    }
}