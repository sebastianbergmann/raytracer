<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const M_PI_2;
use const M_PI_4;
use function array_reverse;
use function deg2rad;
use function sqrt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Camera::class)]
#[UsesClass(Canvas::class)]
#[UsesClass(CanvasIterator::class)]
#[UsesClass(Color::class)]
#[UsesClass(Intersection::class)]
#[UsesClass(IntersectionCollection::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Plane::class)]
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

        $this->assertTrue($image->pixelAt(6, 6)->equalTo(Color::from(0.38066, 0.47583, 0.2855)));
    }

    /**
     * @param int<1, 9>    $horizontalSize
     * @param int<1, 9>    $verticalSize
     * @param int<30, 120> $fieldOfView
     * @param int<2, 8>    $distanceOfCamera
     * @param int<-3, 3>   $heightOfCamera
     * @param int<2, 10>   $distanceOfLight
     * @param int<-10, 10> $heightOfLight
     */
    #[Property(examples: 25)]
    #[TestWith([5, 5, 60, 5, 0, 10, 0], 'sphere in the center of the image')]
    #[TestWith([4, 3, 90, 3, 2, 5, 10], 'camera and light above the sphere')]
    public function test_the_image_of_a_scene_that_is_symmetric_is_symmetric(
        int $horizontalSize,
        int $verticalSize,
        int $fieldOfView,
        int $distanceOfCamera,
        int $heightOfCamera,
        int $distanceOfLight,
        int $heightOfLight,
    ): void {
        // A sphere at the origin, lit and looked at from positions that are
        // neither to the left nor to the right of the sphere: the left half
        // and the right half of this scene mirror each other
        $world = new World;

        $world->add(Sphere::default());
        $world->setLight(PointLight::from(Tuple::point(0, $heightOfLight, -$distanceOfLight), Color::from(1, 1, 1)));

        $camera = Camera::from($horizontalSize, $verticalSize, deg2rad($fieldOfView));

        $camera->setTransform(
            Transformations::view(
                Tuple::point(0, $heightOfCamera, -$distanceOfCamera),
                Tuple::point(0, 0, 0),
                Tuple::vector(0, 1, 0),
            ),
        );

        $image = $camera->render($world);

        $this->assertSame($horizontalSize, $image->width());
        $this->assertSame($verticalSize, $image->height());

        for ($y = 1; $y <= $verticalSize; $y++) {
            $row = [];

            for ($x = 1; $x <= $horizontalSize; $x++) {
                $row[] = $image->pixelAt($x, $y);
            }

            $this->assertEqualsWithDelta(array_reverse($row), $row, 0.000001);
        }
    }

    /**
     * @param int<1, 9>    $horizontalSize
     * @param int<1, 9>    $verticalSize
     * @param int<30, 120> $fieldOfView
     */
    #[Property(examples: 25)]
    #[TestWith([1, 1, 90], 'one pixel')]
    #[TestWith([3, 2, 90], 'more than one row and more than one column')]
    public function test_each_pixel_of_an_image_has_the_color_at_the_ray_for_this_pixel(int $horizontalSize, int $verticalSize, int $fieldOfView): void
    {
        // The camera looks down at a plane from above, so that the ray for
        // each of its pixels hits the plane and no pixel is black
        $world = new World;

        $world->add(Plane::default());
        $world->setLight(PointLight::from(Tuple::point(0, 10, 0), Color::from(1, 1, 1)));

        $camera = Camera::from($horizontalSize, $verticalSize, deg2rad($fieldOfView));

        $camera->setTransform(
            Transformations::view(
                Tuple::point(0, 5, 0),
                Tuple::point(0, 0, 0),
                Tuple::vector(0, 0, 1),
            ),
        );

        $image = $camera->render($world);

        for ($y = 1; $y <= $verticalSize; $y++) {
            for ($x = 1; $x <= $horizontalSize; $x++) {
                $this->assertGreaterThan(0.0, $image->pixelAt($x, $y)->red());
                $this->assertEquals($world->colorAt($camera->rayForPixel($x - 1, $y - 1)), $image->pixelAt($x, $y));
            }
        }
    }
}
