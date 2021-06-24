<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function array_values;
use function count;
use Countable;
use IteratorAggregate;

final class IntersectionCollection implements Countable, IteratorAggregate
{
    /**
     * @psalm-var list<Intersection>
     */
    private array $intersections;

    public static function from(Intersection ...$intersections): self
    {
        return new self(array_values($intersections));
    }

    /**
     * @psalm-param list<Intersection> $intersections
     */
    private function __construct(array $intersections)
    {
        $this->intersections = $intersections;
    }

    /**
     * @psalm-return list<Intersection>
     */
    public function asArray(): array
    {
        return $this->intersections;
    }

    /**
     * @throws OutOfBoundsException
     */
    public function at(int $position): Intersection
    {
        if (!isset($this->intersections[$position])) {
            throw new OutOfBoundsException;
        }

        return $this->intersections[$position];
    }

    public function count(): int
    {
        return count($this->intersections);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNotEmpty(): bool
    {
        return $this->count() > 0;
    }

    public function getIterator(): IntersectionCollectionIterator
    {
        return new IntersectionCollectionIterator($this);
    }
}
