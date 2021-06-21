<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const M_PI;
use function range;
use function round;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 *
 * @medium
 */
final class Chapter4Test extends TestCase
{
    public function test_we_can_draw_12_dots_on_a_circle(): void
    {
        $size   = 200;
        $radius = 75;
        $black  = Color::create(0.0, 0.0, 0.0);
        $white  = Color::create(255.0, 255.0, 255.0);
        $canvas = Canvas::create($size, $size, $black);
        $twelve = Tuple::point(0, 0, 1);

        foreach (range(1, 12) as $hour) {
            $rotation = Matrix::rotationAroundY($hour * (M_PI / 6));
            $point    = $rotation->multiplyBy($twelve);

            $x = (int) round($point->x() * $radius + $size / 2);
            $y = (int) round($point->z() * $radius + $size / 2);

            $canvas->writePixel($x, $y, $white);
        }

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/clock.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }
}
