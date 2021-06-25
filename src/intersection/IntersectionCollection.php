<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function array_values;
use function count;
use function usort;
use Countable;
use IteratorAggregate;

final class IntersectionCollection implements Countable, IteratorAggregate
{
    /**
     * @psalm-var list<Intersection>
     */
    private array $intersections;

    private ?Intersection $hit = null;

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

        $this->process();
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

    /**
     * @psalm-assert-if-true !null $this->hit
     */
    public function hasHit(): bool
    {
        return $this->hit !== null;
    }

    /**
     * @throws IntersectionHasNoHitException
     */
    public function hit(): Intersection
    {
        if ($this->hit === null) {
            throw new IntersectionHasNoHitException;
        }

        return $this->hit;
    }

    private function process(): void
    {
        usort(
            $this->intersections,
            static function (Intersection $a, Intersection $b): int {
                return $a->t() <=> $b->t();
            }
        );

        foreach ($this->intersections as $intersection) {
            if ($intersection->t() > 0) {
                $this->hit = $intersection;

                break;
            }
        }
    }
}