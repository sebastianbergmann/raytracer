<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function array_merge;
use function count;
use function usort;
use Countable;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<int, Intersection>
 *
 * @immutable
 */
final readonly class IntersectionCollection implements Countable, IteratorAggregate
{
    /**
     * @var list<Intersection>
     */
    private array $intersections;
    private ?Intersection $hit;

    public static function from(Intersection ...$intersections): self
    {
        usort(
            $intersections,
            static function (Intersection $a, Intersection $b): int
            {
                return $a->t() <=> $b->t();
            },
        );

        $hit = null;

        foreach ($intersections as $intersection) {
            if ($intersection->t() > 0) {
                $hit = $intersection;

                break;
            }
        }

        return new self($intersections, $hit);
    }

    /**
     * @param list<Intersection> $intersections
     */
    private function __construct(array $intersections, ?Intersection $hit)
    {
        $this->intersections = $intersections;
        $this->hit           = $hit;
    }

    /**
     * @return list<Intersection>
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
     * @phpstan-assert-if-true !null $this->hit
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
                $other->asArray(),
            ),
        );
    }
}
