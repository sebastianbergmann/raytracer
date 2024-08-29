<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
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
}
