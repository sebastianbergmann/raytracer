<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function count;
use Iterator;

final class ObjectCollectionIterator implements Iterator
{
    /**
     * @psalm-var list<Object_>
     */
    private array $objects;

    private int $position = 0;

    public function __construct(ObjectCollection $objects)
    {
        $this->objects = $objects->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < count($this->objects);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): Object_
    {
        return $this->objects[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
