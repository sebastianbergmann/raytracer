<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\PointLight
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class PointLightTest extends TestCase
{
    public function test_a_point_light_has_a_position_and_intensity(): void
    {
        $intensity = Color::from(1, 1, 1);
        $position  = Tuple::point(0, 0, 0);

        $light = PointLight::from($position, $intensity);

        $this->assertSame($position, $light->position());
        $this->assertSame($intensity, $light->intensity());
    }
}
