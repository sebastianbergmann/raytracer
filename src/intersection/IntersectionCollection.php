<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function array_merge;
use function array_values;
use function count;
use function usort;
use Countable;
use IteratorAggregate;

/**
 * @psalm-immutable
 */
final class IntersectionCollection implements Countable, IteratorAggregate
{
    /**
     * @psalm-var list<Intersection>
     */
    private array $intersections;

    private ?Intersection $hit;

    /**
     * @psalm-mutation-free
     */
    public static function from(Intersection ...$intersections): self
    {
        usort(
            $intersections,
            static function (Intersection $a, Intersection $b): int {
                return $a->t() <=> $b->t();
            }
        );

        $hit = null;

        foreach ($intersections as $intersection) {
            if ($intersection->t() > 0) {
                $hit = $intersection;

                break;
            }
        }

        return new self(array_values($intersections), $hit);
    }

    /**
     * @psalm-param list<Intersection> $intersections
     */
    private function __construct(array $intersections, ?Intersection $hit)
    {
        $this->intersections = $intersections;
        $this->hit           = $hit;
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
        if (!$this->hasHit()) {
            throw new IntersectionHasNoHitException;
        }

        return $this->hit;
    }

    public function merge(self $other): self
    {
        return self::from(
            ...array_merge(
                $this->intersections,
                $other->asArray()
            )
        );
    }
}
