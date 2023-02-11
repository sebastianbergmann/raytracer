<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const M_PI_2;
use const M_PI_4;
use function sqrt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Camera::class)]
#[UsesClass(Canvas::class)]
#[UsesClass(Color::class)]
#[UsesClass(Intersection::class)]
#[UsesClass(IntersectionCollection::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(PointLight::class)]
#[UsesClass(PreparedComputation::class)]
#[UsesClass(Ray::class)]
#[UsesClass(Shape::class)]
#[UsesClass(ShapeCollection::class)]
#[UsesClass(ShapeCollectionIterator::class)]
#[UsesClass(Sphere::class)]
#[UsesClass(Transformations::class)]
#[UsesClass(Tuple::class)]
#[UsesClass(World::class)]
#[Small]
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
        $this->assertEqualsWithDelta(0.999, $camera->halfView(), 0.001);
        $this->assertEqualsWithDelta(1.333, $camera->aspect(), 0.001);
        $this->assertEqualsWithDelta(0.999, $camera->cameraHalfWidth(), 0.001);
        $this->assertSame(0.75, $camera->cameraHalfHeight());
    }

    public function test_the_pixel_size_for_a_horizontal_canvas(): void
    {
        $camera = Camera::from(200, 125, M_PI_2);

        $this->assertEqualsWithDelta(0.01, $camera->pixelSize(), 0.00000000000000001);
    }

    public function test_the_pixel_size_for_a_vertical_canvas(): void
    {
        $camera = Camera::from(125, 200, M_PI_2);

        $this->assertEqualsWithDelta(0.01, $camera->pixelSize(), 0.00000000000000001);
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
