<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Plane::class)]
#[UsesClass(Color::class)]
#[UsesClass(Intersection::class)]
#[UsesClass(IntersectionCollection::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Ray::class)]
#[UsesClass(Shape::class)]
#[UsesClass(Tuple::class)]
#[Group('shape')]
#[Small]
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

    #[Property]
    public function test_the_normal_of_a_plane_is_the_same_at_every_point(
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $point,
    ): void {
        $this->assertEquals(Tuple::vector(0, 1, 0), Plane::default()->normalAt($point));
        $this->assertEquals(Tuple::vector(0, 1, 0), Plane::default()->localNormalAt($point));
    }

    #[Property]
    public function test_a_ray_that_is_parallel_to_a_plane_does_not_intersect_it(
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $origin,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroCoordinates')]
        float $x,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $z,
    ): void {
        $this->assertCount(0, Plane::default()->intersect(Ray::from($origin, Tuple::vector($x, 0, $z))));
        $this->assertCount(0, Plane::default()->intersect(Ray::from($origin, Tuple::vector($z, 0, $x))));
    }

    #[Property]
    public function test_a_ray_that_is_not_parallel_to_a_plane_intersects_it_once_where_the_Y_coordinate_of_the_ray_is_zero(
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $origin,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $direction,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroCoordinates')]
        float $y,
    ): void {
        $plane = Plane::default();
        $ray   = Ray::from($origin, Tuple::vector($direction->x, $y, $direction->z));
        $xs    = $plane->intersect($ray);

        $this->assertCount(1, $xs);
        $this->assertSame($plane, $xs->at(0)->shape());
        $this->assertEqualsWithDelta(0.0, $ray->position($xs->at(0)->t())->y, 0.000000001);

        // The ray intersects the plane in front of its origin exactly when it
        // starts above the plane and points down or starts below the plane
        // and points up
        $this->assertSame(($origin->y > 0 && $y < 0) || ($origin->y < 0 && $y > 0), $xs->hasHit());
    }

    #[Property]
    public function test_moving_a_ray_parallel_to_a_plane_does_not_change_when_the_ray_intersects_the_plane(
        #[ForAllExternal(RaytracerGenerators::class, 'rays')]
        Ray $ray,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $x,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $z,
    ): void {
        $plane = Plane::default();
        $moved = Ray::from($ray->origin()->plus(Tuple::vector($x, 0, $z)), $ray->direction());

        $this->assertEquals(
            $plane->intersect($ray)->asArray(),
            $plane->intersect($moved)->asArray(),
        );
    }

    #[TestWith([0.00001, 1])]
    #[TestWith([-0.00001, 1])]
    #[TestWith([0.000009, 0])]
    #[TestWith([-0.000009, 0])]
    public function test_a_ray_is_considered_parallel_to_a_plane_when_the_Y_coordinate_of_its_direction_is_closer_to_zero_than_0_00001(float $y, int $numberOfIntersections): void
    {
        $ray = Ray::from(Tuple::point(0, 1, 0), Tuple::vector(0, $y, 1));

        $this->assertCount($numberOfIntersections, Plane::default()->localIntersect($ray));
    }
}
