<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Sphere
 *
 * @uses \SebastianBergmann\Raytracer\Intersection
 * @uses \SebastianBergmann\Raytracer\IntersectionCollection
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
        $s = new Sphere;

        $this->assertTrue($s->origin()->equalTo(Tuple::point(0, 0, 0)));
        $this->assertSame(1.0, $s->radius());
    }

    public function test_a_ray_intersects_a_sphere_at_two_points(): void
    {
        $r = Ray::from(
            Tuple::point(0, 0, -5),
            Tuple::vector(0, 0, 1)
        );

        $s = new Sphere;

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);

        $this->assertSame(4.0, $xs->at(0)->t());
        $this->assertSame($s, $xs->at(0)->object());

        $this->assertSame(6.0, $xs->at(1)->t());
        $this->assertSame($s, $xs->at(1)->object());
    }

    public function test_a_ray_intersects_a_sphere_at_a_tangent(): void
    {
        $r = Ray::from(
            Tuple::point(0, 1, -5),
            Tuple::vector(0, 0, 1)
        );

        $s = new Sphere;

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);

        $this->assertSame(5.0, $xs->at(0)->t());
        $this->assertSame($s, $xs->at(0)->object());

        $this->assertSame(5.0, $xs->at(1)->t());
        $this->assertSame($s, $xs->at(1)->object());
    }

    public function test_a_ray_misses_a_sphere(): void
    {
        $r = Ray::from(
            Tuple::point(0, 2, -5),
            Tuple::vector(0, 0, 1)
        );

        $s = new Sphere;

        $xs = $s->intersect($r);

        $this->assertEmpty($xs);
    }

    public function test_a_ray_originates_inside_a_sphere(): void
    {
        $r = Ray::from(
            Tuple::point(0, 0, 0),
            Tuple::vector(0, 0, 1)
        );

        $s = new Sphere;

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);

        $this->assertSame(-1.0, $xs->at(0)->t());
        $this->assertSame($s, $xs->at(0)->object());

        $this->assertSame(1.0, $xs->at(1)->t());
        $this->assertSame($s, $xs->at(1)->object());
    }

    public function test_a_sphere_is_behind_a_ray(): void
    {
        $r = Ray::from(
            Tuple::point(0, 0, 5),
            Tuple::vector(0, 0, 1)
        );

        $s = new Sphere;

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);

        $this->assertSame(-6.0, $xs->at(0)->t());
        $this->assertSame(-4.0, $xs->at(1)->t());
    }

    /**
     * @testdox A sphere's default transformation
     */
    public function test_a_spheres_default_transformation(): void
    {
        $s = new Sphere;

        $this->assertTrue($s->transformation()->equalTo(Matrix::identity(4)));
    }

    /**
     * @testdox Changing a sphere's transformation
     */
    public function test_changing_a_spheres_transformation(): void
    {
        $t = Matrix::translation(2, 3, 4);

        $s = new Sphere;
        $s->setTransformation($t);

        $this->assertTrue($s->transformation()->equalTo($t));
    }
}
