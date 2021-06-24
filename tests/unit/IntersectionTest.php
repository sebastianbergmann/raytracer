<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Intersection
 * @covers \SebastianBergmann\Raytracer\IntersectionCollection
 * @covers \SebastianBergmann\Raytracer\IntersectionCollectionIterator
 *
 * @uses \SebastianBergmann\Raytracer\Point
 * @uses \SebastianBergmann\Raytracer\Sphere
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class IntersectionTest extends TestCase
{
    public function test_an_intersection_encapsulates_t_and_object(): void
    {
        $t = 3.5;

        $s = Sphere::from(
            Point::from(0, 0, 0),
            1.0
        );

        $i = Intersection::from($t, $s);

        $this->assertSame($t, $i->t());
        $this->assertSame($s, $i->object());
    }

    public function test_aggregating_intersections(): void
    {
        $s = Sphere::from(
            Point::from(0, 0, 0),
            1.0
        );

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
}
