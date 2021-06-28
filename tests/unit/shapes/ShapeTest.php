<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const M_PI;
use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Shape
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\IntersectionCollection
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Ray
 * @uses \SebastianBergmann\Raytracer\Transformations
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class ShapeTest extends TestCase
{
    public function test_the_default_transformation(): void
    {
        $shape = TestShape::default();

        $this->assertTrue($shape->transform()->equalTo(Matrix::identity(4)));
    }

    public function test_assigning_a_transformation(): void
    {
        $shape     = TestShape::default();
        $transform = Transformations::translation(2, 3, 4);

        $shape->setTransform($transform);

        $this->assertTrue($shape->transform()->equalTo($transform));
    }

    public function test_the_default_material(): void
    {
        $shape = TestShape::default();

        $this->assertTrue($shape->material()->color()->equalTo(Color::from(1, 1, 1)));
        $this->assertSame(0.1, $shape->material()->ambient());
        $this->assertSame(0.9, $shape->material()->diffuse());
        $this->assertSame(0.9, $shape->material()->specular());
        $this->assertSame(200.0, $shape->material()->shininess());
    }

    public function test_assigning_a_material(): void
    {
        $shape    = TestShape::default();
        $material = Material::default();

        $shape->setMaterial($material);

        $this->assertSame($material, $shape->material());
    }

    public function test_intersecting_a_scaled_shape_with_a_ray(): void
    {
        $ray   = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 0, 1));
        $shape = TestShape::default();

        $shape->setTransform(Transformations::scaling(2, 2, 2));

        /* @noinspection UnusedFunctionResultInspection */
        $shape->intersect($ray);

        $this->assertTrue($shape->savedRay()->origin()->equalTo(Tuple::point(0, 0, -2.5)));
        $this->assertTrue($shape->savedRay()->direction()->equalTo(Tuple::vector(0, 0, 0.5)));
    }

    public function test_intersecting_a_translated_shape_with_a_ray(): void
    {
        $ray = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 0, 1));

        $shape = TestShape::default();
        $shape->setTransform(Transformations::translation(5, 0, 0));

        /* @noinspection UnusedFunctionResultInspection */
        $shape->intersect($ray);

        $this->assertTrue($shape->savedRay()->origin()->equalTo(Tuple::point(-5, 0, -5)));
        $this->assertTrue($shape->savedRay()->direction()->equalTo(Tuple::vector(0, 0, 1)));
    }

    public function test_computing_the_normal_on_a_translated_shape(): void
    {
        $shape = TestShape::default();
        $shape->setTransform(Transformations::translation(0, 1, 0));

        $normal = $shape->normalAt(Tuple::point(0, 1.70711, -0.70711));

        $this->assertTrue($normal->equalTo(Tuple::vector(0, 0.70711, -0.70711), 0.00001));
    }

    public function test_computing_the_normal_on_a_transformed_shape(): void
    {
        $shape = TestShape::default();
        $shape->setTransform(Transformations::scaling(1, 0.5, 1)->multiply(Transformations::rotationAroundZ(M_PI / 5)));

        $normal = $shape->normalAt(Tuple::point(0, sqrt(2) / 2, -sqrt(2) / 2));

        $this->assertTrue($normal->equalTo(Tuple::vector(0, 0.97014, -0.24254), 0.00001));
    }
}
