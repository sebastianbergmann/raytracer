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
final class PuttingItTogetherTest extends TestCase
{
    public function test_chapter_4(): void
    {
        $canvasSize = 200;
        $radius     = 75;
        $black      = Color::from(0.0, 0.0, 0.0);
        $white      = Color::from(255.0, 255.0, 255.0);
        $canvas     = Canvas::from($canvasSize, $canvasSize, $black);
        $twelve     = Tuple::point(0, 0, 1);

        foreach (range(1, 12) as $hour) {
            $rotation = Matrix::rotationAroundY($hour * (M_PI / 6));
            $point    = $rotation->multiplyBy($twelve);

            $x = (int) round($point->x() * $radius + $canvasSize / 2);
            $y = (int) round($point->z() * $radius + $canvasSize / 2);

            $canvas->writePixel($x, $y, $white);
        }

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_4.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }
}
