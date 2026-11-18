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

#[CoversClass(Ray::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Transformations::class)]
#[UsesClass(Tuple::class)]
#[Group('math')]
#[Small]
final class RayTest extends TestCase
{
    public function test_creating_and_querying_a_ray(): void
    {
        $origin    = Tuple::point(1, 2, 3);
        $direction = Tuple::vector(4, 5, 6);

        $r = Ray::from($origin, $direction);

        $this->assertSame($origin, $r->origin());
        $this->assertSame($direction, $r->direction());
    }

    public function test_computing_a_point_from_a_distance(): void
    {
        $r = Ray::from(
            Tuple::point(2, 3, 4),
            Tuple::vector(1, 0, 0),
        );

        $this->assertTrue($r->position(0)->equalTo(Tuple::point(2, 3, 4)));
        $this->assertTrue($r->position(1)->equalTo(Tuple::point(3, 3, 4)));
        $this->assertTrue($r->position(-1)->equalTo(Tuple::point(1, 3, 4)));
        $this->assertTrue($r->position(2.5)->equalTo(Tuple::point(4.5, 3, 4)));
    }

    public function test_translating_a_ray(): void
    {
        $r = Ray::from(
            Tuple::point(1, 2, 3),
            Tuple::vector(0, 1, 0),
        );

        $m = Transformations::translation(3, 4, 5);

        $r2 = $r->transform($m);

        $this->assertTrue($r2->origin()->equalTo(Tuple::point(4, 6, 8)));
        $this->assertTrue($r2->direction()->equalTo(Tuple::vector(0, 1, 0)));
    }

    public function test_scaling_a_ray(): void
    {
        $r = Ray::from(
            Tuple::point(1, 2, 3),
            Tuple::vector(0, 1, 0),
        );

        $m = Transformations::scaling(2, 3, 4);

        $r2 = $r->transform($m);

        $this->assertTrue($r2->origin()->equalTo(Tuple::point(2, 6, 12)));
        $this->assertTrue($r2->direction()->equalTo(Tuple::vector(0, 3, 0)));
    }

    #[Property]
    public function test_a_ray_starts_at_its_origin_and_moves_along_its_direction(
        #[ForAllExternal(RaytracerGenerators::class, 'rays')]
        Ray $ray,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $s,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $t,
    ): void {
        $this->assertEquals($ray->origin(), $ray->position(0.0));
        $this->assertEquals($ray->direction()->multiplyBy($t), $ray->position($t)->minus($ray->origin()));
        $this->assertEquals($ray->position($s)->plus($ray->direction()->multiplyBy($t)), $ray->position($s + $t));
    }

    #[Property]
    public function test_transforming_a_ray_transforms_all_of_its_positions(
        #[ForAllExternal(RaytracerGenerators::class, 'rays')]
        Ray $ray,
        #[ForAllExternal(RaytracerGenerators::class, 'transformations')]
        Matrix $transformation,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $t,
    ): void {
        $this->assertEqualsWithDelta(
            $transformation->multiplyBy($ray->position($t)),
            $ray->transform($transformation)->position($t),
            0.0001,
        );
    }

    #[Property]
    public function test_transforming_a_ray_by_a_product_is_transforming_it_by_the_factors_one_after_the_other(
        #[ForAllExternal(RaytracerGenerators::class, 'rays')]
        Ray $ray,
        #[ForAllExternal(RaytracerGenerators::class, 'transformations')]
        Matrix $a,
        #[ForAllExternal(RaytracerGenerators::class, 'transformations')]
        Matrix $b,
    ): void {
        $this->assertEquals($ray, $ray->transform(Matrix::identity(4)));

        $this->assertEqualsWithDelta(
            $ray->transform($a->multiply($b)),
            $ray->transform($b)->transform($a),
            0.0000001,
        );
    }

    #[Property]
    public function test_a_transformed_ray_is_transformed_back_by_the_inverse_of_the_transformation(
        #[ForAllExternal(RaytracerGenerators::class, 'rays')]
        Ray $ray,
        #[ForAllExternal(RaytracerGenerators::class, 'transformations')]
        Matrix $transformation,
    ): void {
        $this->assertEqualsWithDelta(
            $ray,
            $ray->transform($transformation)->transform($transformation->inverse()),
            0.0000001,
        );
    }

    #[Property]
    public function test_a_ray_cannot_be_created_without_a_direction(
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $origin,
    ): void {
        $this->expectException(InvalidArgumentException::class);

        /* @noinspection UnusedFunctionResultInspection */
        Ray::from($origin, Tuple::vector(0, 0, 0));
    }

    #[TestWith([0.0, 0.0, 0.0], 'zero vector')]
    #[TestWith([1e-200, 0.0, 0.0], 'magnitude underflows to zero')]
    public function test_a_ray_cannot_be_created_with_a_direction_that_has_no_magnitude(float $x, float $y, float $z): void
    {
        $this->expectException(InvalidArgumentException::class);

        /* @noinspection UnusedFunctionResultInspection */
        Ray::from(Tuple::point(0, 0, 0), Tuple::vector($x, $y, $z));
    }
}
