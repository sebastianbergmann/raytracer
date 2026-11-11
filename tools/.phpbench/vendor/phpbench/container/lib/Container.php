<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\DependencyInjection;

use Closure;
use Psr\Container\ContainerInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PHPBench Container.
 *
 * This is a simple, extendable, closure based dependency injection container.
 */
class Container implements ContainerInterface
{
    /**
     * @var array<string,Callable>
     */
    private $instantiators = [];

    /**
     * @var array<string, mixed>
     */
    private $services = [];

    /**
     * @var array<string,array<string,mixed>>
     */
    private $tags = [];

    /**
     * @var array<string,mixed>
     */
    private $config = [];

    /**
     * @var array<string>
     */
    private $extensionClasses = [];

    /**
     * @param array<string,mixed> $config
     * @param array<string> $extensionClasses
     */
    public function __construct(array $extensionClasses = [], array $config = [])
    {
        $this->extensionClasses = $extensionClasses;
        $this->config = $config;
    }

    /**
     * Configure the container. This method will call the `configure()` method
     * on each extension. Extensions must use this opportunity to register their
     * services and define any default config.
     *
     * This method must be called before `build()`.
     */
    public function init(): void
    {
        $resolver = new OptionsResolver();
        $extensions = [];
        $config = [];

        if (empty($this->extensionClasses) && empty($this->config)) {
            return;
        }

        foreach ($this->extensionClasses as $extensionClass) {
            if (!class_exists($extensionClass)) {
                throw new \InvalidArgumentException(sprintf(
                    'Extension class "%s" does not exist',
                    $extensionClass
                ));
            }

            $extension = new $extensionClass();

            if (!$extension instanceof ExtensionInterface) {
                throw new \InvalidArgumentException(sprintf(
                    // add any manually specified extensions
                    'Extension "%s" must implement the PhpBench\\Extension interface',
                    get_class($extension)
                ));
            }

            $extensions[] = $extension;
            $extension->configure($resolver);
        }

        $diff = array_diff(array_keys($this->config), array_keys($this->config));

        if ($diff) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown configuration keys: "%s". Permitted keys: "%s"',
                implode('", "', $diff),
                implode('", "', array_keys($this->config))
            ));
        }

        try {
            $this->config = $resolver->resolve($this->config);
        } catch (ExceptionInterface $resolverException) {
            throw new InvalidConfigurationException(sprintf(
                'Invalid user configuration: %s',
                $resolverException->getMessage()
            ), 0, $resolverException);
        }

        foreach ($extensions as $extension) {
            $extension->load($this);
        }
    }

    /**
     * Instantiate and return the service with the given ID.
     * Note that this method will return the same instance on subsequent calls.
     *
     * @param string $serviceId
     */
    public function get($serviceId)
    {
        if (isset($this->services[$serviceId])) {
            return $this->services[$serviceId];
        }

        if (!isset($this->instantiators[$serviceId])) {
            throw new \InvalidArgumentException(sprintf(
                'No instantiator has been registered for requested service "%s"',
                $serviceId
            ));
        }

        $this->services[$serviceId] = $this->instantiators[$serviceId]($this);

        return $this->services[$serviceId];
    }

    /**
     * @param string $serviceId
     */
    public function has($serviceId): bool
    {
        return isset($this->instantiators[$serviceId]);
    }

    /**
     * Set a service instance.
     *
     * @param mixed $instance
     */
    public function set(string $serviceId, $instance): void
    {
        $this->services[$serviceId] = $instance;
    }

    /**
     * Return services IDs for the given tag.
     *
     * @return array<string, array<string, mixed>>
     */
    public function getServiceIdsForTag(string $tag): array
    {
        $serviceIds = [];
        foreach ($this->tags as $serviceId => $tags) {
            if (isset($tags[$tag])) {
                $serviceIds[$serviceId] = $tags[$tag];
            }
        }

        return $serviceIds;
    }

    /**
     * Register a service with the given ID and instantiator.
     *
     * The instantiator is a closure which accepts an instance of this container and
     * returns a new instance of the service class.
     *
     * @param array<string, array<string, mixed>> $tags
     */
    public function register(string $serviceId, Closure $instantiator, array $tags = []): void
    {
        if (isset($this->instantiators[$serviceId])) {
            throw new \InvalidArgumentException(sprintf(
                'Service with ID "%s" has already been registered',
                $serviceId
            ));
        }

        $this->instantiators[$serviceId] = $instantiator;
        $this->tags[$serviceId] = $tags;
    }

    /**
     * Set the value of the parameter with the given name.
     *
     * @param mixed $value
     */
    public function setParameter(string $name, $value): void
    {
        $this->config[$name] = $value;
    }

    /**
     * @param array<mixed> $values
     */
    public function mergeParameter(string $name, array $values): void
    {
        $actual = $this->getParameter($name);

        if (!is_array($actual)) {
            throw new \InvalidArgumentException(sprintf(
                'Cannot merge values on to a scalar parameter "%s"',
                $name
            ));
        }

        $this->setParameter($name, array_merge(
            $actual,
            $values
        ));
    }

    /**
     * Return the parameter with the given name.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        if (!array_key_exists($name, $this->config)) {
            throw new \InvalidArgumentException(sprintf(
                'Parameter "%s" has not been registered',
                $name
            ));
        }

        return $this->config[$name];
    }

    /**
     * @return array<mixed,mixed>
     */
    public function getParameters(): array
    {
        return $this->config;
    }

    /**
     * Return true if the named parameter exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name)
    {
        return array_key_exists($name, $this->config);
    }

    /**
     * @return class-string[]
     */
    public function getExtensionClasses(): array
    {
        return $this->extensionClasses;
    }
}
