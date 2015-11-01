# WebX-Db - PHP IOC Container
Main features and design goals of webx-ioc:
* Resolve an array of implementations of an interface.
* Simple registration of implementations.
* Easy to integrate with non-resolvable parameters.
* Effective (Lazy initialization)
* Light weight (Less than 100 lines).

## Installing
    * Packagist: webx/ioc

## Getting started
```php
    use WebX\Ioc\Ioc;
    use WebX\Ioc\Impl\IocImpl;

    $ioc = new IocImpl();
    $ioc->register(ClassA::class); // Implements InterfaceA

    $a = $ioc->get(InterfaceA::class);
    // Gives the implementing instance (ClassA) of InterfaceA


```
### Resolving multiple instances
```php

    use WebX\Ioc\Ioc;
    use WebX\Ioc\Impl\IocImpl;

    $ioc = new IocImpl();
    $ioc->register(ClassA::class); // Implements InterfaceA
    $ioc->register(ClassAB::class); // Implements both InterfaceA and InterfaceB

    $all = $ioc->getAll(InterfaceA::class);
    // Gives use the implementing instances ([ClassA,ClassAB]) of InterfaceA

```

### Resolving non-resolable parameters
WebX IOC recursively resolves all dependent interfaces upon object creation. Other parameters needs to be defined externally.
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
    echo($a->currency());
    //Returns ClassA's resolved value "USD"

    $ioc = new IocImpl();
    $ioc->register(ClassA::class);
    $ioc->register(ClassB::class);
    $a = $ioc->get(InterfaceA::class);
    echo($a->currency());
    //Returns ClassA's default value "EUR"

```
### Resolving non-resolvable parameters Ex 2
Example of creating a settings file to satisfy parameter dependencies

```json
{
    "mysqli" : {
        "user" : "u",
        "password" : "p",
        "database" : "127.0.0.1"
    }
}

```
settings.json

```php

    use WebX\Ioc\Ioc;
    use WebX\Ioc\Impl\IocImpl;

    class ClassC implements InterfaceC {
        private $mysql;
        public function __construct(\mysqli $mysql) {
            $this->mysql;
        }
    }

    $config = json_decode(file_get_contents("settings.json"),TRUE);

    $resolver = function(\ReflectionParameter $param) use ($config) {
        $key = $param->getDeclaringClass()->getShortName();
        $subKey = $param->getName();
        return isset($config[$key][$subKey]) ? $config[$key][$subKey] : null;
    };

    $ioc = new IocImpl($resolver);
    $ioc->register(ClassC::class);
    $a = $ioc->get(InterfaceA::class);
    // Instantiated \mysqli with the parameters given by the the $resolver function.
    // Instantiated ClassC with the \mysqli instance.

```
Construct parameters for the \mysqli client is provided by a JSON settings file.


## How to run tests
In the root of the project:

  `composer install`

  `phpunit -c tests`
