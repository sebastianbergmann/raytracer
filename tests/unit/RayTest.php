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
        $origin    = Tuple::point(1, 2, 3);
        $direction = Tuple::vector(4, 5, 6);

        $r = Ray::from($origin, $direction);

        $this->assertSame($origin, $r->origin());
        $this->assertSame($direction, $r->direction());
    }
}
