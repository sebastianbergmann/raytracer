<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function count;
use Iterator;

final class IntersectionCollectionIterator implements Iterator
{
    /**
     * @psalm-var list<Intersection>
     */
    private array $intersections;
    private int $position = 0;

    public function __construct(IntersectionCollection $intersections)
    {
        $this->intersections = $intersections->asArray();
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return $this->position < count($this->intersections);
    }

    public function key(): int
    {
        return $this->position;
    }

    public function current(): Intersection
    {
        return $this->intersections[$this->position];
    }

    public function next(): void
    {
        $this->position++;
    }
}
