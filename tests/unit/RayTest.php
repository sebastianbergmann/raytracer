<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Ray
 *
 * @uses \SebastianBergmann\Raytracer\Point
 * @uses \SebastianBergmann\Raytracer\Tuple
 * @uses \SebastianBergmann\Raytracer\Vector
 *
 * @small
 */
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
            Tuple::vector(1, 0, 0)
        );

        $this->assertTrue($r->position(0)->equalTo(Tuple::point(2, 3, 4)));
        $this->assertTrue($r->position(1)->equalTo(Tuple::point(3, 3, 4)));
        $this->assertTrue($r->position(-1)->equalTo(Tuple::point(1, 3, 4)));
        $this->assertTrue($r->position(2.5)->equalTo(Tuple::point(4.5, 3, 4)));
    }
}
