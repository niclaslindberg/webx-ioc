# WebX-Db - PHP IOC Container
Main features and design goals of webx-ioc:
* Resolve an array of implementations of an interface.
* Simple registration of implementations.
* Easy to integrate with non-resolvable parameters.
* Light weight (Less than 100 lines).

## Installing
    * Packagist: webx-ico

## Getting started
```php
    use WebX\Ioc\Ioc;
    use WebX\Ioc\Impl\IocImpl;

    $ioc = new IocImpl();
    $ioc->register(ClassA::class); // Implements InterfaceA

    $a = $ioc->get(InterfaceA::class); // Gives the implementing class (classA) of InterfaceA


```
### Resolving multiple instances
```php

    use WebX\Ioc\Ioc;
    use WebX\Ioc\Impl\IocImpl;

    $ioc = new IocImpl();
    $ioc->register(ClassA::class); // Implements InterfaceA
    $ioc->register(ClassAB::class); // Implements both InterfaceA and InterfaceB

    $all = $ioc->getAll(InterfaceA::class); // Gives use the implementing classes ([classA,classB]) of InterfaceA

```

## How to run tests
In the root of the project:

  `composer install`

  `phpunit -c tests`
