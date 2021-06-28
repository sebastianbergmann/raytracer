<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function count;
use Countable;
use IteratorAggregate;

final class ShapeCollection implements Countable, IteratorAggregate
{
    /**
     * @psalm-var list<Shape>
     */
    private array $shapes = [];

    public function add(Shape $shape): void
    {
        $this->shapes[] = $shape;
    }

    /**
     * @psalm-return list<Shape>
     */
    public function asArray(): array
    {
        return $this->shapes;
    }

    /**
     * @throws OutOfBoundsException
     */
    public function at(int $position): Shape
    {
        if (!isset($this->shapes[$position])) {
            throw new OutOfBoundsException;
        }

        return $this->shapes[$position];
    }

    public function count(): int
    {
        return count($this->shapes);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    public function getIterator(): ShapeCollectionIterator
    {
        return new ShapeCollectionIterator($this);
    }
}
