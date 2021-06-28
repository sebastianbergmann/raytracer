<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function sqrt;
use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Sphere
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Intersection
 * @uses \SebastianBergmann\Raytracer\IntersectionCollection
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Ray
 * @uses \SebastianBergmann\Raytracer\Shape
 * @uses \SebastianBergmann\Raytracer\Transformations
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class SphereTest extends TestCase
{
    public function test_creating_and_querying_a_sphere(): void
    {
        $s = Sphere::default();

        $this->assertTrue($s->origin()->equalTo(Tuple::point(0, 0, 0)));
        $this->assertSame(1.0, $s->radius());
    }

    public function test_a_ray_intersects_a_sphere_at_two_points(): void
    {
        $r = Ray::from(
            Tuple::point(0, 0, -5),
            Tuple::vector(0, 0, 1)
        );

        $s = Sphere::default();

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);

        $this->assertSame(4.0, $xs->at(0)->t());
        $this->assertSame($s, $xs->at(0)->shape());

        $this->assertSame(6.0, $xs->at(1)->t());
        $this->assertSame($s, $xs->at(1)->shape());
    }

    public function test_a_ray_intersects_a_sphere_at_a_tangent(): void
    {
        $r = Ray::from(
            Tuple::point(0, 1, -5),
            Tuple::vector(0, 0, 1)
        );

        $s = Sphere::default();

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);

        $this->assertSame(5.0, $xs->at(0)->t());
        $this->assertSame($s, $xs->at(0)->shape());

        $this->assertSame(5.0, $xs->at(1)->t());
        $this->assertSame($s, $xs->at(1)->shape());
    }

    public function test_a_ray_misses_a_sphere(): void
    {
        $r = Ray::from(
            Tuple::point(0, 2, -5),
            Tuple::vector(0, 0, 1)
        );

        $xs = Sphere::default()->intersect($r);

        $this->assertEmpty($xs);
    }

    public function test_a_ray_originates_inside_a_sphere(): void
    {
        $r = Ray::from(
            Tuple::point(0, 0, 0),
            Tuple::vector(0, 0, 1)
        );

        $s = Sphere::default();

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);

        $this->assertSame(-1.0, $xs->at(0)->t());
        $this->assertSame($s, $xs->at(0)->shape());

        $this->assertSame(1.0, $xs->at(1)->t());
        $this->assertSame($s, $xs->at(1)->shape());
    }

    public function test_a_sphere_is_behind_a_ray(): void
    {
        $r = Ray::from(
            Tuple::point(0, 0, 5),
            Tuple::vector(0, 0, 1)
        );

        $xs = Sphere::default()->intersect($r);

        $this->assertCount(2, $xs);

        $this->assertSame(-6.0, $xs->at(0)->t());
        $this->assertSame(-4.0, $xs->at(1)->t());
    }

    public function test_the_normal_on_a_sphere_at_a_point_on_the_x_axis(): void
    {
        $n = Sphere::default()->normalAt(Tuple::point(1, 0, 0));

        $this->assertTrue($n->equalTo(Tuple::vector(1, 0, 0)));
    }

    public function test_the_normal_on_a_sphere_at_a_point_on_the_y_axis(): void
    {
        $n = Sphere::default()->normalAt(Tuple::point(0, 1, 0));

        $this->assertTrue($n->equalTo(Tuple::vector(0, 1, 0)));
    }

    public function test_the_normal_on_a_sphere_at_a_point_on_the_z_axis(): void
    {
        $n = Sphere::default()->normalAt(Tuple::point(0, 0, 1));

        $this->assertTrue($n->equalTo(Tuple::vector(0, 0, 1)));
    }

    public function test_the_normal_on_a_sphere_at_a_nonaxial_point(): void
    {
        $n = Sphere::default()->normalAt(Tuple::point(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3));

        $this->assertTrue($n->equalTo(Tuple::vector(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3)));
    }

    public function test_the_normal_is_a_normalized_vector(): void
    {
        $n = Sphere::default()->normalAt(Tuple::point(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3));

        $this->assertTrue($n->isVector());
        $this->assertTrue($n->equalTo($n->normalize()));
    }
}
