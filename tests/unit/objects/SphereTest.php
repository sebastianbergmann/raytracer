<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const M_PI;
use function sqrt;
use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Sphere
 *
 * @uses \SebastianBergmann\Raytracer\Intersection
 * @uses \SebastianBergmann\Raytracer\IntersectionCollection
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Ray
 * @uses \SebastianBergmann\Raytracer\Tuple
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

    public function test_intersecting_a_scaled_sphere_with_a_ray(): void
    {
        $r = Ray::from(
            Tuple::point(0, 0, -5),
            Tuple::vector(0, 0, 1)
        );

        $s = new Sphere;
        $s->setTransformation(Matrix::scaling(2, 2, 2));

        $xs = $s->intersect($r);

        $this->assertCount(2, $xs);
        $this->assertSame(3.0, $xs->at(0)->t());
        $this->assertSame(7.0, $xs->at(1)->t());
    }

    public function test_intersecting_a_translated_sphere_with_a_ray(): void
    {
        $r = Ray::from(
            Tuple::point(0, 0, -5),
            Tuple::vector(0, 0, 1)
        );

        $s = new Sphere;
        $s->setTransformation(Matrix::translation(5, 0, 0));

        $xs = $s->intersect($r);

        $this->assertCount(0, $xs);
    }

    public function test_the_normal_on_a_sphere_at_a_point_on_the_x_axis(): void
    {
        $s = new Sphere;

        $n = $s->normalAt(Tuple::point(1, 0, 0));

        $this->assertTrue($n->equalTo(Tuple::vector(1, 0, 0)));
    }

    public function test_the_normal_on_a_sphere_at_a_point_on_the_y_axis(): void
    {
        $s = new Sphere;

        $n = $s->normalAt(Tuple::point(0, 1, 0));

        $this->assertTrue($n->equalTo(Tuple::vector(0, 1, 0)));
    }

    public function test_the_normal_on_a_sphere_at_a_point_on_the_z_axis(): void
    {
        $s = new Sphere;

        $n = $s->normalAt(Tuple::point(0, 0, 1));

        $this->assertTrue($n->equalTo(Tuple::vector(0, 0, 1)));
    }

    public function test_the_normal_on_a_sphere_at_a_nonaxial_point(): void
    {
        $s = new Sphere;

        $n = $s->normalAt(Tuple::point(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3));

        $this->assertTrue($n->equalTo(Tuple::vector(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3)));
    }

    public function test_the_normal_is_a_normalized_vector(): void
    {
        $s = new Sphere;

        $n = $s->normalAt(Tuple::point(sqrt(3) / 3, sqrt(3) / 3, sqrt(3) / 3));

        $this->assertTrue($n->isVector());
        $this->assertTrue($n->equalTo($n->normalize()));
    }

    public function test_computing_the_normal_on_a_translated_sphere(): void
    {
        $s = new Sphere;
        $s->setTransformation(Matrix::translation(0, 1, 0));

        $n = $s->normalAt(Tuple::point(0, 1.70711, -0.70711));

        $this->assertTrue($n->equalTo(Tuple::vector(0, 0.70711, -0.70711), 0.00001));
    }

    public function test_computing_the_normal_on_a_transformed_sphere(): void
    {
        $s = new Sphere;
        $s->setTransformation(Matrix::scaling(1, 0.5, 1)->multiply(Matrix::rotationAroundZ(M_PI / 5)));

        $n = $s->normalAt(Tuple::point(0, sqrt(2) / 2, -sqrt(2) / 2));

        $this->assertTrue($n->equalTo(Tuple::vector(0, 0.97014, -0.24254), 0.00001));
    }
}
