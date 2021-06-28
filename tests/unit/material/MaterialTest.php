<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function sqrt;
use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Material
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Pattern
 * @uses \SebastianBergmann\Raytracer\PointLight
 * @uses \SebastianBergmann\Raytracer\Shape
 * @uses \SebastianBergmann\Raytracer\Sphere
 * @uses \SebastianBergmann\Raytracer\StripePattern
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class MaterialTest extends TestCase
{
    private Material $material;

    private Tuple $position;

    protected function setUp(): void
    {
        $this->material = Material::default();
        $this->position = Tuple::point(0, 0, 0);
    }

    public function test_the_default_material(): void
    {
        $this->assertTrue($this->material->color()->equalTo(Color::from(1, 1, 1)));
        $this->assertSame(0.1, $this->material->ambient());
        $this->assertSame(0.9, $this->material->diffuse());
        $this->assertSame(0.9, $this->material->specular());
        $this->assertSame(200.0, $this->material->shininess());
    }

    public function test_color_can_be_changed(): void
    {
        $color = Color::from(0, 0, 0, );

        $this->material->setColor($color);

        $this->assertSame($color, $this->material->color());
    }

    public function test_ambient_can_be_changed(): void
    {
        $ambient = 1.0;

        $this->material->setAmbient($ambient);

        $this->assertSame($ambient, $this->material->ambient());
    }

    public function test_diffuse_can_be_changed(): void
    {
        $diffuse = 1.0;

        $this->material->setDiffuse($diffuse);

        $this->assertSame($diffuse, $this->material->diffuse());
    }

    public function test_specular_can_be_changed(): void
    {
        $specular = 1.0;

        $this->material->setSpecular($specular);

        $this->assertSame($specular, $this->material->specular());
    }

    public function test_shininess_can_be_changed(): void
    {
        $shininess = 1.0;

        $this->material->setShininess($shininess);

        $this->assertSame($shininess, $this->material->shininess());
    }

    public function test_lighting_with_the_eye_between_the_light_and_the_surface(): void
    {
        $eye    = Tuple::vector(0, 0, -1);
        $normal = Tuple::vector(0, 0, -1);
        $light  = PointLight::from(Tuple::point(0, 0, -10), Color::from(1, 1, 1));

        $result = $this->material->lighting(Sphere::default(), $light, $this->position, $eye, $normal, false);

        $this->assertTrue($result->equalTo(Color::from(1.9, 1.9, 1.9)));
    }

    /**
     * @testdox Lighting with the eye between the light and the surface, eye offset 45°
     */
    public function test_lighting_with_the_eye_between_the_light_and_the_surface_eye_offset_45_degrees(): void
    {
        $eye    = Tuple::vector(0, sqrt(2) / 2, -sqrt(2) / 2);
        $normal = Tuple::vector(0, 0, -1);
        $light  = PointLight::from(Tuple::point(0, 0, -10), Color::from(1, 1, 1));

        $result = $this->material->lighting(Sphere::default(), $light, $this->position, $eye, $normal, false);

        $this->assertTrue($result->equalTo(Color::from(1.0, 1.0, 1.0)));
    }

    /**
     * @testdox Lighting with the eye opposite the surface, light offset 45°
     */
    public function test_lighting_with_the_eye_opposite_the_surface_light_offset_45_degrees(): void
    {
        $eye    = Tuple::vector(0, 0, -1);
        $normal = Tuple::vector(0, 0, -1);
        $light  = PointLight::from(Tuple::point(0, 10, -10), Color::from(1, 1, 1));

        $result = $this->material->lighting(Sphere::default(), $light, $this->position, $eye, $normal, false);

        $this->assertTrue($result->equalTo(Color::from(0.7364, 0.7364, 0.7364)));
    }

    public function test_lighting_with_the_eye_in_the_path_of_the_reflection_vector(): void
    {
        $eye    = Tuple::vector(0, -sqrt(2) / 2, -sqrt(2) / 2);
        $normal = Tuple::vector(0, 0, -1);
        $light  = PointLight::from(Tuple::point(0, 10, -10), Color::from(1, 1, 1));

        $result = $this->material->lighting(Sphere::default(), $light, $this->position, $eye, $normal, false);

        $this->assertTrue($result->equalTo(Color::from(1.6364, 1.6364, 1.6364)));
    }

    public function test_lighting_with_the_light_behind_the_surface(): void
    {
        $eye    = Tuple::vector(0, 0, -1);
        $normal = Tuple::vector(0, 0, -1);
        $light  = PointLight::from(Tuple::point(0, 0, 110), Color::from(1, 1, 1));

        $result = $this->material->lighting(Sphere::default(), $light, $this->position, $eye, $normal, false);

        $this->assertTrue($result->equalTo(Color::from(0.1, 0.1, 0.1)));
    }

    public function test_lighting_with_the_surface_in_shadow(): void
    {
        $eye    = Tuple::vector(0, 0, -1);
        $normal = Tuple::vector(0, 0, -1);
        $light  = PointLight::from(Tuple::point(0, 0, -10), Color::from(1, 1, 1));

        $result = $this->material->lighting(Sphere::default(), $light, $this->position, $eye, $normal, true);

        $this->assertTrue($result->equalTo(Color::from(0.1, 0.1, 0.1)));
    }

    public function test_lighting_with_a_pattern_applied(): void
    {
        $material = Material::default();
        $material->setAmbient(1);
        $material->setDiffuse(0);
        $material->setSpecular(0);
        $material->setPattern(StripePattern::from(Color::from(1, 1, 1), Color::from(0, 0, 0)));

        $eyev    = Tuple::vector(0, 0, -1);
        $normalv = Tuple::vector(0, 0, -1);
        $light   = PointLight::from(Tuple::point(0, 0, -10), Color::from(1, 1, 1));

        $this->assertTrue($material->lighting(Sphere::default(), $light, Tuple::point(0.9, 0, 0), $eyev, $normalv, false)->equalTo(Color::from(1, 1, 1)));
        $this->assertTrue($material->lighting(Sphere::default(), $light, Tuple::point(1.1, 0, 0), $eyev, $normalv, false)->equalTo(Color::from(0, 0, 0)));
    }
}
