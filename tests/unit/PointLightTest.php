<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PointLight::class)]
#[UsesClass(Color::class)]
#[UsesClass(Tuple::class)]
#[Small]
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
