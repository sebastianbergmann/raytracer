<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const M_PI_2;
use const M_PI_4;
use function sqrt;
use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Camera
 *
 * @uses \SebastianBergmann\Raytracer\Canvas
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Intersection
 * @uses \SebastianBergmann\Raytracer\IntersectionCollection
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\ObjectCollection
 * @uses \SebastianBergmann\Raytracer\ObjectCollectionIterator
 * @uses \SebastianBergmann\Raytracer\PointLight
 * @uses \SebastianBergmann\Raytracer\PreparedComputation
 * @uses \SebastianBergmann\Raytracer\Ray
 * @uses \SebastianBergmann\Raytracer\Sphere
 * @uses \SebastianBergmann\Raytracer\Transformations
 * @uses \SebastianBergmann\Raytracer\Tuple
 * @uses \SebastianBergmann\Raytracer\World
 *
 * @small
 */
final class CameraTest extends TestCase
{
    public function test_constructing_a_camera(): void
    {
        $horizontalSize = 160;
        $verticalSize   = 120;
        $fieldOfView    = M_PI_2;

        $camera = Camera::from($horizontalSize, $verticalSize, $fieldOfView);

        $this->assertSame($horizontalSize, $camera->horizontalSize());
        $this->assertSame($verticalSize, $camera->verticalSize());
        $this->assertSame($fieldOfView, $camera->fieldOfView());
        $this->assertTrue($camera->transform()->equalTo(Matrix::identity(4)));
    }

    public function test_the_pixel_size_for_a_horizontal_canvas(): void
    {
        $camera = Camera::from(200, 125, M_PI_2);

        $this->assertSame(0.01, $camera->pixelSize());
    }

    public function test_the_pixel_size_for_a_vertical_canvas(): void
    {
        $camera = Camera::from(125, 200, M_PI_2);

        $this->assertSame(0.01, $camera->pixelSize());
    }

    public function test_constructing_a_ray_through_the_center_of_the_canvas(): void
    {
        $ray = Camera::from(201, 101, M_PI_2)->rayForPixel(100, 50);

        $this->assertTrue($ray->origin()->equalTo(Tuple::point(0, 0, 0)));
        $this->assertTrue($ray->direction()->equalTo(Tuple::vector(0, 0, -1)));
    }

    public function test_constructing_a_ray_through_a_corner_of_the_canvas(): void
    {
        $ray = Camera::from(201, 101, M_PI_2)->rayForPixel(0, 0);

        $this->assertTrue($ray->origin()->equalTo(Tuple::point(0, 0, 0)));
        $this->assertTrue($ray->direction()->equalTo(Tuple::vector(0.66519, 0.33259, -0.66851), 0.00001));
    }

    public function test_constructing_a_ray_when_the_camera_is_transformed(): void
    {
        $camera = Camera::from(201, 101, M_PI_2);
        $camera->setTransform(Transformations::rotationAroundY(M_PI_4)->multiply(Transformations::translation(0, -2, 5)));

        $ray = $camera->rayForPixel(100, 50);

        $this->assertTrue($ray->origin()->equalTo(Tuple::point(0, 2, -5)));
        $this->assertTrue($ray->direction()->equalTo(Tuple::vector(sqrt(2) / 2, 0, -(sqrt(2) / 2))));
    }

    public function test_rendering_a_world_with_a_camera(): void
    {
        $world  = World::default();
        $from   = Tuple::point(0, 0, -5);
        $to     = Tuple::point(0, 0, 0);
        $up     = Tuple::vector(0, 1, 0);
        $camera = Camera::from(11, 11, M_PI_2);

        $camera->setTransform(Transformations::view($from, $to, $up));

        $image = $camera->render($world);

        $this->assertTrue($image->pixelAt(5, 5)->equalTo(Color::from(0.38066, 0.47583, 0.2855)));
    }
}
