<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Sphere
 *
 * @uses \SebastianBergmann\Raytracer\Point
 * @uses \SebastianBergmann\Raytracer\Ray
 * @uses \SebastianBergmann\Raytracer\Tuple
 * @uses \SebastianBergmann\Raytracer\Vector
 *
 * @small
 */
final class SphereTest extends TestCase
{
    public function test_creating_and_querying_a_sphere(): void
    {
        $origin = Point::from(0, 0, 0);
        $radius = 1.0;

        $s = Sphere::from($origin, $radius);

        $this->assertSame($origin, $s->origin());
        $this->assertSame($radius, $s->radius());
    }

    public function test_a_ray_intersects_a_sphere_at_two_points(): void
    {
        $r = Ray::from(
            Point::from(0, 0, -5),
            Vector::from(0, 0, 1)
        );

        $s = Sphere::from(
            Point::from(0, 0, 0),
            1.0
        );

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);
        $this->assertSame(4.0, $xs[0]);
        $this->assertSame(6.0, $xs[1]);
    }

    public function test_a_ray_intersects_a_sphere_at_a_tangent(): void
    {
        $r = Ray::from(
            Point::from(0, 1, -5),
            Vector::from(0, 0, 1)
        );

        $s = Sphere::from(
            Point::from(0, 0, 0),
            1.0
        );

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);
        $this->assertSame(5.0, $xs[0]);
        $this->assertSame(5.0, $xs[1]);
    }

    public function test_a_ray_misses_a_sphere(): void
    {
        $r = Ray::from(
            Point::from(0, 2, -5),
            Vector::from(0, 0, 1)
        );

        $s = Sphere::from(
            Point::from(0, 0, 0),
            1.0
        );

        $xs = $s->intersect($r);

        $this->assertEmpty($xs);
    }

    public function test_a_ray_originates_inside_a_sphere(): void
    {
        $r = Ray::from(
            Point::from(0, 0, 0),
            Vector::from(0, 0, 1)
        );

        $s = Sphere::from(
            Point::from(0, 0, 0),
            1.0
        );

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);
        $this->assertSame(-1.0, $xs[0]);
        $this->assertSame(1.0, $xs[1]);
    }

    public function test_a_sphere_is_behind_a_ray(): void
    {
        $r = Ray::from(
            Point::from(0, 0, 5),
            Vector::from(0, 0, 1)
        );

        $s = Sphere::from(
            Point::from(0, 0, 0),
            1.0
        );

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);
        $this->assertSame(-6.0, $xs[0]);
        $this->assertSame(-4.0, $xs[1]);
    }
}
