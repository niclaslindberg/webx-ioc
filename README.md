# WebX-Ioc - PHP IOC Container
Why choose WebX-Ioc
* Easy to integrate - No external dependencies
* Extremely flexible.
* Very fast & light weight (< 170 lines, lazy initialization, resolution cache).
* No external dependencies.

## Installing
Packagist: `webx/ioc` http://packagist.org/packages/webx/ioc

## Getting started
To get started the IOC container must be initialized and implementations must be registered.

```php
    use WebX\Ioc\Ioc;
    use WebX\Ioc\Util\Bootstrap;   //Ready to use bootstrapper.
```
#### Resolving an instance by interface
```php
    class ClassA implements InterfaceA {}

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassA::class);

    $a = $ioc->get(InterfaceA::class);
    // objectA - instance of classA (implements InterfaceA).
    $a2 = $ioc->get(InterfaceA::class);
    echo($a===$a2); // true
```

#### Resolving multiple instances of the same interface
```php
    class ClassA implements InterfaceA {}
    class ClassAB implements InterfaceA, InterfaceB {}

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassA::class);
    $ioc->register(ClassAB::class);

    $allA = $ioc->getAll(InterfaceA::class);
    // [objectA,objectAB] - array of all instances of InterfaceA.

    $allB = $ioc->getAll(InterfaceB::class);
    // [objectAB] - array of all instances of InterfaceB.

```

#### Resolving same instance from multiple interfaces
```php
    class ClassAB implements InterfaceA,InterfaceB {}

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassAB::class);

    $a = $ioc->get(InterfaceA::class);
    $b = $ioc->get(InterfaceB::class);
    echo($a===$b); // true
```


#### Registering an already existing instance
The container supports registration of already existing instances to be resolved by their interfaces.
```php
    class ClassA implements InterfaceA {}

    $a = new ClassA(); // Instantiated outside the container.
    $ioc = Bootstrap::ioc();
    $ioc->register($a);

    $a2 = $ioc->get(InterfaceA::class);
    echo($a===$a2); // true
```

### Configuring instances
Web/Ioc allows instances to be configured. Configuration is done with the optional
configuration array on the 'register()' function. All values are optional.

```php

    $config = [
        "id" => (string)"someId",
        // Unique id (per interface type) for the registered instance.
        "parameters" => (array) [
            "constructorParam1" => (string)
            "constructorParamN" => (string)
            //If constructor parameter is
            array and the value is string it will be used as interface-type-hint for resolving instances of type.
            class and the value is string it will be used as id to find class instance
            else the value will be set for the contructor param
        ],
        "factory" => (Closure) control instantiation by a dependency injection supported Closure.
            //Ex: function(IA $resolvedInstance) {
            //  return new ClassA($resolvedInstance)
            //}
            //Note: "parameters" are supported for factory closure arguments.
        "class" => bool //If the container should also publish the instance by it's class name
    ];

    $ioc->register(SomeClass::class,$config);
```

#### Registering a named instance or class
```php
    class ClassA implements InterfaceA {}

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassA::class,["id"=>"id1"]);
    $ioc->register(ClassA::class,["id"=>"id2"]);

    $a1 = $ioc->get(InterfaceA::class,"id1");
    $a2 = $ioc->get(InterfaceA::class,"id2");
    echo($a1 !== $a2); // true
```

#### Resolving an instance by its class name
```php
    class ClassA implements InterfaceA {}

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassA::class,["class"=>true]);

    $a1 = $ioc->get(ClassA::class);
    echo($a1 instanceof ClassA); // true
```

#### Registering a named instance and configuring a mapping to it
```php
    class ClassA implements InterfaceA {}

    class ClassB implements InterfaceB {

        public $a;

        public function __construct(InterfaceA $paramA) {
            $this->a = $a;
        }
    }

    $ioc = Bootstrap::ioc();
    $a1 = new ClassA();
    $a2 = new ClassA();
    $ioc->register($a1,["id"=>"id1"]);
    $ioc->register($a2,["id"=>"id2"]);

    $ioc->register(ClassB::class,["parameters"=>["paramA"=>"id2"]]);
    //Causes the constructor param 'paramA' of class 'ClassB' to use instance 'id2'

    $b = $ioc->get(InterfaceB::class);
    echo($a2 === $b->a); // true
```
#### Registering an instance with a predefined constructor parameter
```php
    class ClassA implements InterfaceA {

        public $someVar;
        public $b;

        public function __construct($someVar,InterfaceB $b) {
            $this->someVar = $someVar;
            $this->b = $b;
        }

    }

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassA::class,["parameters"=>["someVar"=>"someValue"]]);
    $ioc->register(ClassB::class);

    $a = $ioc->get(InterfaceA::class);
    echo($a->someVar); // "someValue"
```
#### Controlling instantiation with a factory closure
```php

    class ClassA implements InterfaceA {}

    class ClassB implements InterfaceB {}

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassB::class);
    $ioc->register(InterfaceA::class, ["factory" => function(InterfaceB $b){ //Scans the concrete class
                                                                         //ClassA for interfaces
        return new ClassA($b);
    }]);
    $a = $ioc->get(InterfaceA::class);

```

#### Instantiate a non-registered class
If you want to instantiate a class with dependency injection
```php



    class ClassA implements InterfaceA {

        public function __construct() {}

        public function sayWhat() {
            return "Here I am!";
        }
    }

    class ClassB  {

        private $a;

        public function __constructor(InterfaceA $a) {
            $this->a = $a;
        }
        public function saySomething() {
            return $this->a->sayWhat;
        }
    }

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassA::class);

    $b = $ioc->instantiate(ClassB::class);
    echo($a->saySomething()); // "Here I am!"
```

#### Invoke a a `Closure`
Closures may be invoked with its dependencies automatically resolved
```php

    class ClassA implements InterfaceA {

        public function __construct() {}

        public function sayWhat() {
            return "Here I am!";
        }
    }

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassA::class);

    $result = $ioc->invoke(function(InterfaceA $a) {
        return $a->sayWhat();
    });
    echo($result); // "Here I am!"
```

### Resolving non-resolvable parameters
WebX/Ioc recursively tries to resolve all dependent interfaces upon object creation. Other dependencies must be resolved externally.
#### Example 1
```php
    class ClassA implements InterfaceA {
        private $b;
        private $currency;

        public function __construct(InterfaceB $b, $currency="EUR") {
        //$b is automatically resolved by the container (ClassB is registered).
        //$currency is not an interface and will be resolved by the resolver function
            $this->b = $b;
            $this->currency = $currency;
        }
        public function currency() {
            return $currency;
        }
    }

    // Will be invoked whenever the container needs
    // to resolve an non-resolvable parameter.
    $resolver = function(IocNonResolvable $nonResolvable, Ioc $ioc) {
        if($param->name()==='currency') {
            return "USD";
        }
    };

    Bootstrap::init($resolver);
    $iocWithResolver = Bootstrap::ioc();
    $iocWithResolver->register(ClassA::class);
    $iocWithResolver->register(ClassB::class);
    $a = $iocWithResolver->get(InterfaceA::class);
    echo($a->currency());
    //Returns ClassA's resolved value for $currency "USD"

    $ioc = Bootstrap::ioc();
    $ioc->register(ClassA::class);
    $ioc->register(ClassB::class);
    $a = $ioc->get(InterfaceA::class);
    echo($a->currency());
    //Returns ClassA's default value for $currency "EUR"

```

### Utilities
* ```WebX\Ioc\Util\Bootstrap``` Simple, easy to use bootstrapper for a single shared instance of Ioc. Statically accessible.

### How to run tests
In the root of the project:
```bash
    composer install
    phpunit -c tests
```

### Related projects
* `webx/db` https://github.com/niclaslindberg/webx-db
* `webx/routes` https://github.com/niclaslindberg/webx-routes
