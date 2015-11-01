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

### Resolving non-resolable parameters
WebX IOC recursivly resolves all dependent interfaces upon object creation. Other parameters needs to be defined externally.
```php

    use WebX\Ioc\Ioc;
    use WebX\Ioc\Impl\IocImpl;

    class ClassA implements InterfaceA {
        private $currency;

        public function __construct(InterfaceB $b, $currency="EUR") {
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
    $iocWithResolver->register(ClassB::class);
    $iocWithResolver->register(ClassA::class);
    $a = $iocWithResolver->get(InterfaceA::class);
    echo($a->currency())    //Returns ClassA's resolved value "USD"

    $ioc = new IocImpl();
    $ioc->register(ClassA::class);
    $ioc->register(ClassB::class);
    $a = $ioc->get(InterfaceA::class);
    echo($a->currency())    //Returns ClassA's default value "EUR"

```
### Resolving non-resolable parameters Ex 2

```php

    use WebX\Ioc\Ioc;
    use WebX\Ioc\Impl\IocImpl;

    $config = [
        "user" => "u",
        "password" => "p",
        "database" => "test",
        "host" => "127.0.0.1",
    ]

    $resolver = function(\ReflectionParameter $param) use ($config) {
        if($param->getDeclaringClass()->getName()==)'\\mysqli') { //Only provide parameters for \mysqli
            return isset($config[$param->getName()]) ? $config[$param->getName()] : null;
        }
    };

    class ClassC implements InterfaceC {
        private $mysql;

        public function __construct(\mysqli $mysql) {
            $this->mysql;
        }
    }

    $ioc = new IocImpl($resolver);
    $ioc->register(ClassC::class);
    $a = $iocWithResolver->get(InterfaceA::class);
    // WebX Ioc now instantiated ClassC with an instance of \mysqli with the
    // parameters given by the the $resolver function.

```


## How to run tests
In the root of the project:

  `composer install`

  `phpunit -c tests`
