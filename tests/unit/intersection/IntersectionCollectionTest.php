<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
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
}
