<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const M_PI;
use const M_PI_2;
use const M_PI_4;
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
            $rotation = Transformations::rotationAroundY($hour * (M_PI / 6));
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

        $material = Material::default();
        $material->setColor(Color::from(1, 0.2, 1));

        $s = new Sphere;
        $s->setMaterial($material);

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
                    $color  = $hit->object()->material()->lighting($light, $point, $eye, $normal, false);

                    $canvas->writePixel($x, $y, $color);
                }
            }
        }

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_6.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }

    public function test_chapter_7(): void
    {
        $floor = new Sphere;
        $floor->setTransformation(Transformations::scaling(10, 0.01, 10));
        $floorMaterial = Material::default();
        $floorMaterial->setColor(Color::from(1, 0.9, 0.9));
        $floorMaterial->setSpecular(0);
        $floor->setMaterial($floorMaterial);

        $leftWall = new Sphere;
        $leftWall->setTransformation(
            Transformations::translation(0, 0, 5)->multiply(
                Transformations::rotationAroundY(-M_PI_4)
            )->multiply(
                Transformations::rotationAroundX(M_PI_2)
            )->multiply(
                Transformations::scaling(10, 0.01, 10)
            )
        );
        $leftWall->setMaterial($floorMaterial);

        $rightWall = new Sphere;
        $rightWall->setTransformation(
            Transformations::translation(0, 0, 5)->multiply(
                Transformations::rotationAroundY(M_PI_4)
            )->multiply(
                Transformations::rotationAroundX(M_PI_2)
            )->multiply(
                Transformations::scaling(10, 0.01, 10)
            )
        );
        $rightWall->setMaterial($floorMaterial);

        $middle = new Sphere;
        $middle->setTransformation(Transformations::translation(-0.5, 1, 0.5));
        $middleMaterial = Material::default();
        $middleMaterial->setColor(Color::from(0.1, 1, 0.5));
        $middleMaterial->setDiffuse(0.7);
        $middleMaterial->setSpecular(0.3);
        $middle->setMaterial($middleMaterial);

        $right = new Sphere;
        $right->setTransformation(Transformations::translation(1.5, 0.5, -0.5)->multiply(Transformations::scaling(0.5, 0.5, 0.5)));
        $rightMaterial = Material::default();
        $rightMaterial->setColor(Color::from(0.5, 1, 0.1));
        $rightMaterial->setDiffuse(0.7);
        $rightMaterial->setSpecular(0.3);
        $right->setMaterial($rightMaterial);

        $left = new Sphere;
        $left->setTransformation(Transformations::translation(-1.5, 0.33, -0.75)->multiply(Transformations::scaling(0.33, 0.33, 0.33)));
        $leftMaterial = Material::default();
        $leftMaterial->setColor(Color::from(1, 0.8, 0.1));
        $leftMaterial->setDiffuse(0.7);
        $leftMaterial->setSpecular(0.3);
        $left->setMaterial($leftMaterial);

        $world = new World;
        $world->objects()->add($floor);
        $world->objects()->add($leftWall);
        $world->objects()->add($rightWall);
        $world->objects()->add($middle);
        $world->objects()->add($right);
        $world->objects()->add($left);
        $world->setLight(PointLight::from(Tuple::point(-10, 10, -10), Color::from(1, 1, 1)));

        $camera = Camera::from(100, 50, M_PI / 3);
        $camera->setTransform(
            Transformations::view(
                Tuple::point(0, 1.5, -5),
                Tuple::point(0, 1, 0),
                Tuple::vector(0, 1, 0)
            )
        );

        $canvas = $camera->render($world);

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_7.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }
}
