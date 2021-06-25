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
        $black      = Color::from(0, 0, 0);
        $white      = Color::from(1, 1, 1);
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

    public function test_chapter_5(): void
    {
        $canvasSize = 150;
        $black      = Color::from(0, 0, 0);
        $red        = Color::from(1, 0, 0);
        $canvas     = Canvas::from($canvasSize, $canvasSize, $black);

        $s         = new Sphere;
        $rayOrigin = Tuple::point(0, 0, -5);

        $wallZ     = 10;
        $wallSize  = 7.0;
        $pixelSize = $wallSize / $canvasSize;
        $halfSize  = $wallSize / 2;

        foreach (range(1, $canvasSize) as $x) {
            foreach (range(1, $canvasSize) as $y) {
                $worldX   = -$halfSize + $pixelSize * $x;
                $worldY   = $halfSize - $pixelSize * $y;
                $position = Tuple::point($worldX, $worldY, $wallZ);
                $ray      = Ray::from($rayOrigin, $position->minus($rayOrigin));

                if ($s->intersect($ray)->hasHit()) {
                    $canvas->writePixel($x, $y, $red);
                }
            }
        }

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_5.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }

    public function test_chapter_6(): void
    {
        $canvasSize = 150;
        $black      = Color::from(0.0, 0.0, 0.0);
        $canvas     = Canvas::from($canvasSize, $canvasSize, $black);

        $s = new Sphere;
        $s->setMaterial(
            Material::from(
                Color::from(1, 0.2, 1),
                0.1,
                0.9,
                0.9,
                200.0
            )
        );

        $light = PointLight::from(
            Tuple::point(-10, 10, -10),
            Color::from(1, 1, 1)
        );

        $rayOrigin = Tuple::point(0, 0, -5);

        $wallZ     = 10;
        $wallSize  = 7.0;
        $pixelSize = $wallSize / $canvasSize;
        $halfSize  = $wallSize / 2;

        foreach (range(1, $canvasSize) as $x) {
            foreach (range(1, $canvasSize) as $y) {
                $worldX   = -$halfSize + $pixelSize * $x;
                $worldY   = $halfSize - $pixelSize * $y;
                $position = Tuple::point($worldX, $worldY, $wallZ);
                $ray      = Ray::from($rayOrigin, $position->minus($rayOrigin)->normalize());

                if ($s->intersect($ray)->hasHit()) {
                    $hit    = $s->intersect($ray)->hit();
                    $point  = $ray->position($hit->t());
                    $normal = $hit->object()->normalAt($point);
                    $eye    = $ray->direction()->negate();
                    $color  = $hit->object()->material()->lighting($light, $point, $eye, $normal);

                    $canvas->writePixel($x, $y, $color);
                }
            }
        }

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_6.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }
}
