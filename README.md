# WebX-Ioc - PHP IOC Container
Main features and design goals of webx-ioc:
* Resolve instance(single) and array of instances(list) of an interface.
* Simple setup and configuration.
* Easy to extend to resolve non-resolvable dependencies.
* Easy to integrate with any framework / application.
* Very fast & light weight (< 100 lines, lazy initialization, etc).
* No external dependencies.

## Installing
    * Packagist: webx/ioc

## Getting started
To get started the IOC container must be initialized and implementations must be registered.
```php
    use WebX\Ioc\Ioc;
    use WebX\Ioc\Impl\IocImpl;

    $ioc = new IocImpl();
    $ioc->register(ClassA::class); // Implements InterfaceA

    $a = $ioc->get(InterfaceA::class);
    // Returns an implementation of InterfaceA (ClassA).
```

#### Resolving multiple instances of the same interface
```php

    $ioc = new IocImpl();
    $ioc->register(ClassA::class); // Implements InterfaceA
    $ioc->register(ClassAB::class); // Implements InterfaceA and InterfaceB

    $all = $ioc->getAll(InterfaceA::class);
    // Returns an array of implementations of InterfaceA ([ClassA,ClassAB]).

```

#### Resolving same instance from multiple interfaces
```php

    $ioc = new IocImpl();
    $ioc->register(ClassAB::class); // Implements InterfaceA and InterfaceB

    $a = $ioc->get(InterfaceA::class);
    $b = $ioc->get(InterfaceB::class);
    echo($a===$b); // true
```

### Resolving non-resolvable parameters
WebX IOC recursively resolves all registered dependent interfaces upon object creation. Other dependencies must be resolved externally.
#### Example 1
```php

    class ClassA implements InterfaceA {
        private $b;
        private $currency;

        public function __construct(InterfaceB $b, $currency="EUR") {
        //$b is automatically resolved by the container ($classB is registered).
        //$currency is not an interface and will be resolved by the resolver function
            $this->b = $b;
            $this->currency = $currency;
        }
        public function currency() {
            return $currency;
        }
    }

    // Will be invoked whenever the container needs to resolve an non-resolvable parameter.
    $resolver = function(\ReflectionParameter $param) {
        if($param->name()==='currency') {
            return "USD";
        }
    };

    $iocWithResolver = new IocImpl($resolver);
    $iocWithResolver->register(ClassA::class);
    $iocWithResolver->register(ClassB::class);
    $a = $iocWithResolver->get(InterfaceA::class);
    echo($a->currency());
    //Returns ClassA's resolved value for $currency "USD"

    $ioc = new IocImpl();
    $ioc->register(ClassA::class);
    $ioc->register(ClassB::class);
    $a = $ioc->get(InterfaceA::class);
    echo($a->currency());
    //Returns ClassA's default value for $currency "EUR"

```
#### Example 2
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
