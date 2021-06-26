<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function count;
use Countable;
use IteratorAggregate;

final class ObjectCollection implements Countable, IteratorAggregate
{
    /**
     * @psalm-var list<Object_>
     */
    private array $objects = [];

    public function add(Object_ $object): void
    {
        $this->objects[] = $object;
    }

    /**
     * @psalm-return list<Object_>
     */
    public function asArray(): array
    {
        return $this->objects;
    }

    /**
     * @throws OutOfBoundsException
     */
    public function at(int $position): Object_
    {
        if (!isset($this->objects[$position])) {
            throw new OutOfBoundsException;
        }

        return $this->objects[$position];
    }

    public function count(): int
    {
        return count($this->objects);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    public function getIterator(): ObjectCollectionIterator
    {
        return new ObjectCollectionIterator($this);
    }
}
