<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Ray
 *
 * @small
 */
final class RayTest extends TestCase
{
    public function test_creating_and_querying_a_ray(): void
    {
        $origin    = Point::from(1, 2, 3);
        $direction = Vector::from(4, 5, 6);

        $r = Ray::from($origin, $direction);

        $this->assertSame($origin, $r->origin());
        $this->assertSame($direction, $r->direction());
    }

    public function test_computing_a_point_from_a_distance(): void
    {
        $r = Ray::from(
            Point::from(2, 3, 4),
            Vector::from(1, 0, 0)
        );

        $this->assertTrue($r->position(0)->equalTo(Point::from(2, 3, 4)));
        $this->assertTrue($r->position(1)->equalTo(Point::from(3, 3, 4)));
        $this->assertTrue($r->position(-1)->equalTo(Point::from(1, 3, 4)));
        $this->assertTrue($r->position(2.5)->equalTo(Point::from(4.5, 3, 4)));
    }
}
