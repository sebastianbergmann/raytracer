<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Intersection
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Sphere
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class IntersectionTest extends TestCase
{
    public function test_an_intersection_encapsulates_t_and_object(): void
    {
        $t = 3.5;

        $s = new Sphere;

        $i = Intersection::from($t, $s);

        $this->assertSame($t, $i->t());
        $this->assertSame($s, $i->object());
    }
}