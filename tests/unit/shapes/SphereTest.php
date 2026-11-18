<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function sort;
use function sqrt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\RequireClassification;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Sphere::class)]
#[UsesClass(Color::class)]
#[UsesClass(Intersection::class)]
#[UsesClass(IntersectionCollection::class)]
#[UsesClass(IntersectionCollectionIterator::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Ray::class)]
#[UsesClass(Shape::class)]
#[UsesClass(Transformations::class)]
#[UsesClass(Tuple::class)]
#[Group('shape')]
#[Small]
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
            Tuple::vector(0, 0, 1),
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
            Tuple::vector(0, 0, 1),
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
            Tuple::vector(0, 0, 1),
        );

        $xs = Sphere::default()->intersect($r);

        $this->assertEmpty($xs);
    }

    public function test_a_ray_originates_inside_a_sphere(): void
    {
        $r = Ray::from(
            Tuple::point(0, 0, 0),
            Tuple::vector(0, 0, 1),
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
            Tuple::vector(0, 0, 1),
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

    #[Property]
    #[RequireClassification('hit', 25.0)]
    #[RequireClassification('miss', 10.0)]
    public function test_a_ray_intersects_a_sphere_where_it_passes_through_the_surface_of_the_sphere(
        #[ForAllExternal(RaytracerGenerators::class, 'raysNearTheUnitSphere')]
        Ray $ray,
    ): void {
        $sphere = Sphere::default();
        $xs     = $sphere->intersect($ray);

        if ($xs->isEmpty()) {
            $this->classify('miss');

            $this->assertCount(0, $xs);

            return;
        }

        $this->classify('hit');

        $this->assertCount(2, $xs);
        $this->assertLessThanOrEqual($xs->at(1)->t(), $xs->at(0)->t());

        foreach ($xs as $x) {
            $this->assertSame($sphere, $x->shape());

            $this->assertEqualsWithDelta(
                $sphere->radius(),
                $ray->position($x->t())->minus($sphere->origin())->magnitude(),
                0.000000001,
            );
        }
    }

    #[Property]
    #[RequireClassification('hit', 25.0)]
    #[RequireClassification('miss', 10.0)]
    public function test_a_ray_intersects_a_sphere_exactly_when_its_distance_from_the_center_of_the_sphere_is_not_greater_than_the_radius(
        #[ForAllExternal(RaytracerGenerators::class, 'raysNearTheUnitSphere')]
        Ray $ray,
    ): void {
        $sphere = Sphere::default();

        // The distance of a line from a point is the magnitude of the cross
        // product of a vector from the point to the line and the direction
        // of the line, divided by the magnitude of the direction
        $product = $ray->origin()->minus($sphere->origin())->cross($ray->direction());

        $isNotFartherAwayThanTheRadius = $product->dot($product) <= $sphere->radius() ** 2 * $ray->direction()->dot($ray->direction());

        $this->classify($isNotFartherAwayThanTheRadius ? 'hit' : 'miss');

        $this->assertSame($isNotFartherAwayThanTheRadius, $sphere->intersect($ray)->isNotEmpty());
    }

    #[Property]
    public function test_a_ray_that_passes_through_the_inside_of_a_sphere_intersects_it_at_two_different_points(
        #[ForAllExternal(RaytracerGenerators::class, 'raysThroughTheUnitSphere')]
        Ray $ray,
    ): void {
        $xs = Sphere::default()->intersect($ray);

        $this->assertCount(2, $xs);
        $this->assertLessThan($xs->at(1)->t(), $xs->at(0)->t());
    }

    #[Property]
    public function test_a_ray_that_starts_inside_a_sphere_intersects_it_behind_and_in_front_of_its_origin(
        #[ForAllExternal(RaytracerGenerators::class, 'pointsInsideTheUnitSphere')]
        Tuple $origin,
        #[ForAllExternal(RaytracerGenerators::class, 'nearNonZeroVectors')]
        Tuple $direction,
    ): void {
        $xs = Sphere::default()->intersect(Ray::from($origin, $direction));

        $this->assertCount(2, $xs);
        $this->assertLessThan(0.0, $xs->at(0)->t());
        $this->assertGreaterThan(0.0, $xs->at(1)->t());
        $this->assertSame($xs->at(1), $xs->hit());
    }

    #[Property]
    public function test_multiplying_the_direction_of_a_ray_by_a_factor_divides_the_times_at_which_it_intersects_a_sphere_by_this_factor(
        #[ForAllExternal(RaytracerGenerators::class, 'raysThroughTheUnitSphere')]
        Ray $ray,
        #[ForAllExternal(RaytracerGenerators::class, 'scalingFactors')]
        float $factor,
    ): void {
        $sphere = Sphere::default();
        $scaled = Ray::from($ray->origin(), $ray->direction()->multiplyBy($factor));

        $ts = [];

        foreach ($sphere->intersect($ray) as $x) {
            $ts[] = $x->t() / $factor;
        }

        sort($ts);

        $this->assertCount(2, $sphere->intersect($scaled));
        $this->assertEqualsWithDelta($ts[0], $sphere->intersect($scaled)->at(0)->t(), 0.000000001);
        $this->assertEqualsWithDelta($ts[1], $sphere->intersect($scaled)->at(1)->t(), 0.000000001);
    }

    #[Property]
    public function test_transforming_a_sphere_and_a_ray_in_the_same_way_does_not_change_when_the_ray_intersects_the_sphere(
        #[ForAllExternal(RaytracerGenerators::class, 'raysThroughTheUnitSphere')]
        Ray $ray,
        #[ForAllExternal(RaytracerGenerators::class, 'transformations')]
        Matrix $transformation,
    ): void {
        $sphere      = Sphere::default();
        $transformed = Sphere::default();

        $transformed->setTransform($transformation);

        $expected = $sphere->intersect($ray);
        $actual   = $transformed->intersect($ray->transform($transformation));

        $this->assertCount(2, $actual);
        $this->assertEqualsWithDelta($expected->at(0)->t(), $actual->at(0)->t(), 0.000001);
        $this->assertEqualsWithDelta($expected->at(1)->t(), $actual->at(1)->t(), 0.000001);
    }

    #[Property]
    public function test_the_normal_on_a_sphere_is_a_unit_vector_that_is_perpendicular_to_the_surface_of_the_sphere(
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $fromCenterToSurface,
        #[ForAllExternal(RaytracerGenerators::class, 'nearNonZeroVectors')]
        Tuple $v,
        #[ForAllExternal(RaytracerGenerators::class, 'transformations')]
        Matrix $transformation,
    ): void {
        $sphere = Sphere::default();

        $sphere->setTransform($transformation);

        // A point on the surface of the unit sphere and a vector that is
        // tangent to the unit sphere at this point, before and after the
        // unit sphere is transformed
        $point   = Tuple::point($fromCenterToSurface->x, $fromCenterToSurface->y, $fromCenterToSurface->z);
        $tangent = $fromCenterToSurface->cross($v);

        $transformedPoint   = $transformation->multiplyBy($point);
        $transformedTangent = $transformation->multiplyBy($tangent);

        $normal = $sphere->normalAt($transformedPoint);

        $this->assertTrue($normal->isVector());
        $this->assertEqualsWithDelta(1.0, $normal->magnitude(), 0.000000001);
        $this->assertEqualsWithDelta(0.0, $normal->dot($transformedTangent), 0.000000001 * (1 + $transformedTangent->magnitude()));
        $this->assertEqualsWithDelta($fromCenterToSurface, Sphere::default()->normalAt($point), 0.000000001);
    }
}
