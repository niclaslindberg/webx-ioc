# WebX-Db - PHP IOC Container
Main features and design goals of webx-ioc:
* Resolve an array of implementations of an interface.
* Simple registration of implementations.
* Easy to integrate with non-resolvable parameters.
* Effective (Lazy initialization)
* Light weight (Less than 100 lines).

## Installing
    * Packagist: webx-ioc

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

    $all = $ioc->getAll(InterfaceA::class); // Gives use the implementing classes ([classA,classAB]) of InterfaceA

```

### Resolving non-resolving parameters
```php

    use WebX\Ioc\Ioc;
    use WebX\Ioc\Impl\IocImpl;

    class ClassA implements InterfaceA {
        private $currency;
        public function __construct(IB $b, $currency="EUR") {
            $this->currency = $currency;
        }
        public function currency() {
            return $currency;
        }
    }

    $resolver = function(\ReflectionParameter $param) {
        if($param->name()==='currency') {
            return "USD";
        }
    };

    $iocWithResolver = new IocImpl($resolver);
    $iocWithResolver->register(ClassA::class);
    $a = $iocWithResolver->get(InterfaceA::class);
    echo($a->currency())    //Returns ClassA's resolved value "USD"

    $ioc = new IocImpl();
    $ioc->register(ClassA::class); 
    $a = $ioc->get(InterfaceA::class);
    echo($a->currency())    //Returns ClassA's default value "EUR"

```


## How to run tests
In the root of the project:

  `composer install`

  `phpunit -c tests`
