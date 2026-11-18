<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function array_filter;
use function array_map;
use function array_merge;
use function array_reverse;
use function count;
use function iterator_to_array;
use function min;
use function sort;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAll;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\RequireClassification;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\PropertyBased\Generators;
use PHPUnit\Framework\PropertyBased\ValueGenerator;
use PHPUnit\Framework\TestCase;

#[CoversClass(Intersection::class)]
#[CoversClass(IntersectionCollection::class)]
#[CoversClass(IntersectionCollectionIterator::class)]
#[UsesClass(Color::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Shape::class)]
#[UsesClass(Sphere::class)]
#[UsesClass(Tuple::class)]
#[Group('intersection')]
#[Small]
final class IntersectionCollectionTest extends TestCase
{
    /**
     * Only one in six of the lists that RaytracerGenerators::intersectionTimes()
     * generates has no positive number, so half of the lists that are
     * generated here only have numbers that are not positive.
     *
     * @return ValueGenerator<list<float>>
     */
    public static function intersectionTimesOfWhichManyAreNotPositive(): ValueGenerator
    {
        return Generators::oneOf(
            RaytracerGenerators::intersectionTimes(),
            Generators::lists(
                Generators::integers(-8, 0)->map(
                    static fn (int $halves): float => $halves / 2,
                ),
                0,
                8,
            ),
        );
    }

    public function test_aggregating_intersections(): void
    {
        $s  = Sphere::default();
        $i1 = Intersection::from(1, $s);
        $i2 = Intersection::from(2, $s);
        $xs = IntersectionCollection::from($i1, $i2);

        $this->assertCount(2, $xs);
        $this->assertTrue($xs->isNotEmpty());
        $this->assertFalse($xs->isEmpty());

        $this->assertSame($i1, $xs->at(0));
        $this->assertSame($i2, $xs->at(1));

        $number = 0;

        foreach ($xs as $i => $intersection) {
            $this->assertSame($number, $i);

            if ($i === 0) {
                $this->assertSame($i1, $intersection);
            } else {
                $this->assertSame($i2, $intersection);
            }

            $number++;
        }

        $this->expectException(OutOfBoundsException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $xs->at(2);
    }

    public function test_can_be_merged(): void
    {
        $s   = Sphere::default();
        $i1  = Intersection::from(1, $s);
        $i2  = Intersection::from(2, $s);
        $xs1 = IntersectionCollection::from($i1);
        $xs2 = IntersectionCollection::from($i2);

        $xs3 = $xs1->merge($xs2);

        $this->assertCount(2, $xs3);
        $this->assertTrue($xs3->isNotEmpty());
        $this->assertFalse($xs3->isEmpty());

        $this->assertSame($i1, $xs3->at(0));
        $this->assertSame($i2, $xs3->at(1));
    }

    public function test_the_hit_when_all_intersections_have_positive_t(): void
    {
        $s  = Sphere::default();
        $i1 = Intersection::from(1, $s);
        $i2 = Intersection::from(2, $s);
        $xs = IntersectionCollection::from($i2, $i1);

        $this->assertTrue($xs->hasHit());
        $this->assertSame($i1, $xs->hit());
    }

    public function test_the_hit_when_some_intersections_have_negative_t(): void
    {
        $s  = Sphere::default();
        $i1 = Intersection::from(-1, $s);
        $i2 = Intersection::from(1, $s);
        $xs = IntersectionCollection::from($i2, $i1);

        $this->assertTrue($xs->hasHit());
        $this->assertSame($i2, $xs->hit());
    }

    public function test_the_hit_when_all_intersections_have_negative_t(): void
    {
        $s  = Sphere::default();
        $i1 = Intersection::from(-2, $s);
        $i2 = Intersection::from(-1, $s);
        $xs = IntersectionCollection::from($i2, $i1);

        $this->assertFalse($xs->hasHit());

        $this->expectException(IntersectionHasNoHitException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $xs->hit();
    }

    public function test_the_hit_is_always_the_lowest_nonnegative_intersection(): void
    {
        $s  = Sphere::default();
        $i1 = Intersection::from(5, $s);
        $i2 = Intersection::from(7, $s);
        $i3 = Intersection::from(-3, $s);
        $i4 = Intersection::from(2, $s);
        $xs = IntersectionCollection::from($i1, $i2, $i3, $i4);

        $this->assertTrue($xs->hasHit());
        $this->assertSame($i4, $xs->hit());
    }

    /**
     * @param list<float> $ts
     */
    #[Property]
    public function test_intersections_are_sorted_by_t_in_whatever_order_they_are_given(
        #[ForAllExternal(RaytracerGenerators::class, 'intersectionTimes')]
        array $ts,
    ): void {
        $intersections = $this->intersectionsAt($ts);
        $collection    = IntersectionCollection::from(...$intersections);

        sort($ts);

        $this->assertSame($ts, $this->timesOf($collection));
        $this->assertSame($ts, $this->timesOf(IntersectionCollection::from(...array_reverse($intersections))));
        $this->assertCount(count($ts), $collection);
        $this->assertSame($ts === [], $collection->isEmpty());
        $this->assertSame($ts !== [], $collection->isNotEmpty());
        $this->assertSame($collection->asArray(), iterator_to_array($collection));

        foreach ($intersections as $intersection) {
            $this->assertContains($intersection, $collection->asArray());
        }
    }

    /**
     * @param list<float> $ts
     */
    #[Property]
    #[RequireClassification('hit', 25.0)]
    #[RequireClassification('no hit', 25.0)]
    public function test_the_hit_is_the_intersection_with_the_lowest_positive_t(
        #[ForAll('intersectionTimesOfWhichManyAreNotPositive')]
        array $ts,
    ): void {
        $collection = IntersectionCollection::from(...$this->intersectionsAt($ts));
        $positive   = array_filter($ts, static fn (float $t): bool => $t > 0);

        if ($positive !== []) {
            $this->classify('hit');

            $this->assertTrue($collection->hasHit());
            $this->assertSame(min($positive), $collection->hit()->t());

            return;
        }

        $this->classify('no hit');

        $this->assertFalse($collection->hasHit());

        $this->expectException(IntersectionHasNoHitException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $collection->hit();
    }

    /**
     * @param list<float> $ts1
     * @param list<float> $ts2
     */
    #[Property]
    public function test_merging_gives_the_collection_of_the_intersections_of_both_collections(
        #[ForAllExternal(RaytracerGenerators::class, 'intersectionTimes')]
        array $ts1,
        #[ForAllExternal(RaytracerGenerators::class, 'intersectionTimes')]
        array $ts2,
    ): void {
        $xs1 = IntersectionCollection::from(...$this->intersectionsAt($ts1));
        $xs2 = IntersectionCollection::from(...$this->intersectionsAt($ts2));

        $merged = $xs1->merge($xs2);

        $this->assertCount(count($ts1) + count($ts2), $merged);
        $this->assertSame($this->timesOf(IntersectionCollection::from(...$this->intersectionsAt(array_merge($ts1, $ts2)))), $this->timesOf($merged));
        $this->assertSame($this->timesOf($merged), $this->timesOf($xs2->merge($xs1)));
        $this->assertSame($xs1->asArray(), $xs1->merge(IntersectionCollection::from())->asArray());
        $this->assertSame($xs1->asArray(), IntersectionCollection::from()->merge($xs1)->asArray());
        $this->assertSame($xs1->hasHit() || $xs2->hasHit(), $merged->hasHit());

        foreach ([$xs1, $xs2] as $collection) {
            if ($collection->hasHit()) {
                $this->assertLessThanOrEqual($collection->hit()->t(), $merged->hit()->t());
                $this->assertContains($merged->hit(), array_merge($xs1->asArray(), $xs2->asArray()));
            }
        }
    }

    /**
     * @param list<float> $ts
     */
    #[Property]
    public function test_an_intersection_can_only_be_accessed_at_a_position_from_zero_to_the_number_of_intersections_minus_one(
        #[ForAllExternal(RaytracerGenerators::class, 'intersectionTimes')]
        array $ts,
    ): void {
        $collection = IntersectionCollection::from(...$this->intersectionsAt($ts));

        foreach ($collection->asArray() as $position => $intersection) {
            $this->assertSame($intersection, $collection->at($position));
        }

        $position = $this->draw(
            Generators::oneOf(
                Generators::integers(maximum: -1),
                Generators::integers(minimum: count($ts)),
            ),
            'position that is negative or not less than the number of intersections',
        );

        $this->expectException(OutOfBoundsException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $collection->at($position);
    }

    /**
     * @param list<float> $ts
     *
     * @return list<Intersection>
     */
    private function intersectionsAt(array $ts): array
    {
        $shape = Sphere::default();

        return array_map(
            static fn (float $t): Intersection => Intersection::from($t, $shape),
            $ts,
        );
    }

    /**
     * @return list<float>
     */
    private function timesOf(IntersectionCollection $collection): array
    {
        return array_map(
            static fn (Intersection $intersection): float => $intersection->t(),
            $collection->asArray(),
        );
    }
}
