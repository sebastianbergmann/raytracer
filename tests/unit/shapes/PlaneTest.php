<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Plane
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\IntersectionCollection
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Ray
 * @uses \SebastianBergmann\Raytracer\Shape
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class PlaneTest extends TestCase
{
    public function test_the_normal_of_a_plane_is_constant_everywhere(): void
    {
        $plane = Plane::default();

        $this->assertTrue($plane->normalAt(Tuple::point(0, 0, 0))->equalTo(Tuple::vector(0, 1, 0)));
        $this->assertTrue($plane->normalAt(Tuple::point(10, 0, -0))->equalTo(Tuple::vector(0, 1, 0)));
        $this->assertTrue($plane->normalAt(Tuple::point(-5, 0, 150))->equalTo(Tuple::vector(0, 1, 0)));
    }

    public function test_intersect_with_a_ray_parallel_to_the_plane(): void
    {
        $plane = Plane::default();
        $ray   = Ray::from(Tuple::point(0, 10, 0), Tuple::vector(0, 0, 1));

        $intersections = $plane->localIntersect($ray);

        $this->assertEmpty($intersections);
    }

    public function test_intersect_with_a_coplanar_ray(): void
    {
        $plane = Plane::default();
        $ray   = Ray::from(Tuple::point(0, 0, 0), Tuple::vector(0, 0, 1));

        $intersections = $plane->localIntersect($ray);

        $this->assertEmpty($intersections);
    }

    public function test_a_ray_intersecting_a_plane_from_above(): void
    {
        $plane = Plane::default();
        $ray   = Ray::from(Tuple::point(0, 1, 0), Tuple::vector(0, -1, 0));

        $intersections = $plane->localIntersect($ray);

        $this->assertCount(1, $intersections);

        $this->assertSame(1.0, $intersections->at(0)->t());
        $this->assertSame($plane, $intersections->at(0)->shape());
    }

    public function test_a_ray_intersecting_a_plane_from_below(): void
    {
        $plane = Plane::default();
        $ray   = Ray::from(Tuple::point(0, -1, 0), Tuple::vector(0, 1, 0));

        $intersections = $plane->localIntersect($ray);

        $this->assertCount(1, $intersections);

        $this->assertSame(1.0, $intersections->at(0)->t());
        $this->assertSame($plane, $intersections->at(0)->shape());
    }
}
