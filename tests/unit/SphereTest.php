<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Sphere
 *
 * @small
 */
final class SphereTest extends TestCase
{
    public function test_creating_and_querying_a_sphere(): void
    {
        $origin = Tuple::point(0, 0, 0);
        $radius = 1.0;

        $s = Sphere::from($origin, $radius);

        $this->assertSame($origin, $s->origin());
        $this->assertSame($radius, $s->radius());
    }
}
