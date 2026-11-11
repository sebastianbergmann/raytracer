PHPBench Service Container
==========================

[![Build Status](https://travis-ci.org/phpbench/container.svg?branch=master)](https://travis-ci.org/phpbench/container)

Simple, extensible dependency injection container with parameters and service
tagging. Implements [container
interop](https://github.com/container-interop/container-interop).

Simple usage
------------

```php
$container = new Container();
$container->register('foobar', function (Container $container) {
    return new \stdClass();
});
```

Extending and Tagging
---------------------

Extension classes should be passed as the first argument to the container (the
user configuration is the second argumnet).

```php
$container = new Container(
    [
        MyExtension::class
    ],
    [
        'foo.bar' => 'my_new_value',
    ]
);
$container->init(); // will trigger loading of the extensions.
```

```php
class MyExtension implements ExtensionInterface
{
    public function load(Container $container)
    {
        $container->register('my_service', function (Container $container) {
            $service = new MyService(
                $container->getParameter('foo_bar'),
                $container->get('some_other_service')
            );

            foreach ($container->getServiceIdsForTag('tag') as $serviceId => $params) {
                $service->add($container->get($serviceId));
            }

            return $service;
        });

        $container->register('tagged_service', function (Container $container) {
            return new MyService(
                $container->getParameter('foo_bar'),
                $container->get('some_other_service')
            );
        }, [ 'tag' => [ 'param1' => 'foobar' ]);
    }

    /**
     * Return the default parameters for the container.
     *
     * @return array
     */
    public function getDefaultConfig()
    {
        return [
            'foo_bar' => 'this is foo'
        ];
    }
}
```
