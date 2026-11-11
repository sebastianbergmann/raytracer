<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\DependencyInjection\Tests\Unit;

use InvalidArgumentException;
use PhpBench\DependencyInjection\Container;
use PhpBench\DependencyInjection\ExtensionInterface;
use PHPUnit\Framework\TestCase;
use PhpBench\DependencyInjection\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContainerTest extends TestCase
{
    private $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    /**
     * It should register and get services
     * It should return the same instance on consecutive calls.
     */
    public function testRegisterSet()
    {
        $this->container->register('stdclass', function () {
            return new \stdClass();
        });

        $instance = $this->container->get('stdclass');
        $this->assertInstanceOf('stdClass', $instance);
        $this->assertSame($instance, $this->container->get('stdclass'));
    }

    /**
     * It should say if it contains a service or not.
     */
    public function testHas()
    {
        $this->container->register('stdclass', function () {
            return new \stdClass();
        });

        $this->assertFalse($this->container->has('foo'));
        $this->assertTrue($this->container->has('stdclass'));
    }

    /**
     * It should register and retrieve tagged services IDs with attributes.
     */
    public function testServiceIdTags()
    {
        $this->container->register('stdclass1', function () {
            return new \stdClass();
        }, ['tag1' => ['name' => 'hello']]);
        $this->container->register('stdclass2', function () {
            return new \stdClass();
        }, ['tag1' => ['name' => 'hello']]);

        $this->container->register('stdclass3', function () {
            return new \stdClass();
        }, ['tag2' => ['name' => 'goodbye']]);

        $serviceIds = $this->container->getServiceIdsForTag('tag1');
        $this->assertNotNull($serviceIds);
        $this->assertCount(2, $serviceIds);

        foreach ($serviceIds as $attributes) {
            $this->assertEquals('hello', $attributes['name']);
        }
    }

    /**
     * Its should throw an exception if a service is already registered.
     */
    public function testServiceAlreadyRegistered()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Service with ID "stdclass"');
        $this->container->register('stdclass', function () {
            return new \stdClass();
        });
        $this->container->register('stdclass', function () {
            return new \stdClass();
        });
    }

    /**
     * It should register extensions.
     * It should register extension configuration.
     * It should build the extensions.
     */
    public function testRegisterExtension()
    {
        $container = new Container([
            __NAMESPACE__ . '\\TestExtension',
        ]);

        $container->init();
        $object = $container->get('foobar');
        $this->assertInstanceOf('stdClass', $object);
        $this->assertEquals('bar', $object->foobar);
        $this->assertEquals([
            __NAMESPACE__ . '\\TestExtension',
        ], $container->getExtensionClasses());;
    }

    /**
     * User configuration should take priority over extension configuration.
     */
    public function testRegisterExtensionWithUserConfig()
    {
        $container = new Container(
            [
                __NAMESPACE__ . '\\TestExtension',
            ],
            [
                'foo' => 'bazz',
            ]
        );

        $container->init();
        $object = $container->get('foobar');
        $this->assertInstanceOf('stdClass', $object);
        $this->assertEquals('bazz', $object->foobar);
    }

    /**
     * It should merge parameters.
     */
    public function testMergeParameters()
    {
        $this->container->setParameter('foo', ['foo' => 'bar']);
        $this->container->mergeParameter('foo', ['bar' => 'boo']);
        $this->assertEquals([
            'foo' => 'bar',
            'bar' => 'boo',
        ], $this->container->getParameter('foo'));
    }

    /**
     * It should throw an exception when trying to merge a value into a non-array parameter.
     */
    public function testMergeParameterNonArray()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('scalar');

        $this->container->setParameter('foo', 'bar');
        $this->container->mergeParameter('foo', ['bar' => 'boo']);
    }

    /**
     * It should throw an exception if an extension class does not exist.
     */
    public function testRegisterNotExistingExtension()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('"NotExistingExtension" does not exist');

        $container = new Container(['NotExistingExtension']);
        $container->init();
    }

    /**
     * It should throw an exception if an extension class does not implement
     * the ExtensionInterface.
     */
    public function testRegisterNotImplementingExtension()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Extension "stdClass" must implement the');

        $container = new Container(['stdClass']);
        $container->init();
    }

    /**
     * It should throw an exception if an unknown user configuration key is used.
     */
    public function testUnknownUserConfig()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('Invalid user configuration');
        $container = new Container([], [
            'not' => 'existing',
        ]);
        $container->init();
    }

    /**
     * It should throw an exception if a requested parameter does not exist.
     */
    public function testUnknownParameter()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter "foo" has not been registered');

        $container = new Container();
        $container->getParameter('foo');
    }
}

class TestExtension implements ExtensionInterface
{
    public function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'foo' => 'bar',
        ]);
    }

    public function load(Container $container): void
    {
        $container->register('foobar', function ($container) {
            $stdClass = new \stdClass();
            $stdClass->foobar = $container->getParameter('foo');

            return $stdClass;
        });
    }
}
