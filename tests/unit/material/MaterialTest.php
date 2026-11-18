<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function abs;
use function sqrt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UseGenerators;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Material::class)]
#[UsesClass(Color::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Pattern::class)]
#[UsesClass(PointLight::class)]
#[UsesClass(Shape::class)]
#[UsesClass(Sphere::class)]
#[UsesClass(StripePattern::class)]
#[UsesClass(Tuple::class)]
#[UseGenerators(RaytracerGenerators::class)]
#[Group('material')]
#[Small]
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
        $color = Color::from(0, 0, 0);

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

    #[TestDox('Lighting with the eye between the light and the surface, eye offset 45°')]
    public function test_lighting_with_the_eye_between_the_light_and_the_surface_eye_offset_45_degrees(): void
    {
        $eye    = Tuple::vector(0, sqrt(2) / 2, -sqrt(2) / 2);
        $normal = Tuple::vector(0, 0, -1);
        $light  = PointLight::from(Tuple::point(0, 0, -10), Color::from(1, 1, 1));

        $result = $this->material->lighting(Sphere::default(), $light, $this->position, $eye, $normal, false);

        $this->assertTrue($result->equalTo(Color::from(1.0, 1.0, 1.0)));
    }

    #[TestDox('Lighting with the eye opposite the surface, light offset 45°')]
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
        $material->setPattern(Pattern::stripe(Color::from(1, 1, 1), Color::from(0, 0, 0)));

        $eyev    = Tuple::vector(0, 0, -1);
        $normalv = Tuple::vector(0, 0, -1);
        $light   = PointLight::from(Tuple::point(0, 0, -10), Color::from(1, 1, 1));

        $this->assertTrue($material->lighting(Sphere::default(), $light, Tuple::point(0.9, 0, 0), $eyev, $normalv, false)->equalTo(Color::from(1, 1, 1)));
        $this->assertTrue($material->lighting(Sphere::default(), $light, Tuple::point(1.1, 0, 0), $eyev, $normalv, false)->equalTo(Color::from(0, 0, 0)));
    }

    #[Property]
    public function test_a_point_in_shadow_is_lit_the_same_from_wherever_it_is_looked_at_and_whatever_its_surface_faces(
        Material $material,
        #[ForAllExternal(RaytracerGenerators::class, 'nearPoints')]
        Tuple $point,
        #[ForAllExternal(RaytracerGenerators::class, 'nearNonZeroVectors')]
        Tuple $fromPointToLight,
        #[ForAllExternal(RaytracerGenerators::class, 'displayableColors')]
        Color $intensity,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $eye,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $normal,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $otherEye,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $otherNormal,
    ): void {
        $shape = Sphere::default();
        $light = PointLight::from($point->plus($fromPointToLight), $intensity);

        $this->assertEquals(
            $material->lighting($shape, $light, $point, $eye, $normal, true),
            $material->lighting($shape, $light, $point, $otherEye, $otherNormal, true),
        );
    }

    #[Property]
    public function test_a_point_is_never_brighter_when_it_is_in_shadow(
        Material $material,
        #[ForAllExternal(RaytracerGenerators::class, 'nearPoints')]
        Tuple $point,
        #[ForAllExternal(RaytracerGenerators::class, 'nearNonZeroVectors')]
        Tuple $fromPointToLight,
        #[ForAllExternal(RaytracerGenerators::class, 'displayableColors')]
        Color $intensity,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $eye,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $normal,
    ): void {
        $shape = Sphere::default();
        $light = PointLight::from($point->plus($fromPointToLight), $intensity);

        $inShadow = $material->lighting($shape, $light, $point, $eye, $normal, true);
        $inLight  = $material->lighting($shape, $light, $point, $eye, $normal, false);

        $this->assertGreaterThanOrEqual(0.0, $inShadow->red());
        $this->assertGreaterThanOrEqual(0.0, $inShadow->green());
        $this->assertGreaterThanOrEqual(0.0, $inShadow->blue());
        $this->assertLessThanOrEqual($inLight->red(), $inShadow->red());
        $this->assertLessThanOrEqual($inLight->green(), $inShadow->green());
        $this->assertLessThanOrEqual($inLight->blue(), $inShadow->blue());
    }

    #[Property]
    public function test_a_point_is_lit_as_if_it_were_in_shadow_when_the_light_is_behind_its_surface(
        Material $material,
        #[ForAllExternal(RaytracerGenerators::class, 'nearPoints')]
        Tuple $point,
        #[ForAllExternal(RaytracerGenerators::class, 'displayableColors')]
        Color $intensity,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $eye,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $normal,
        #[ForAllExternal(RaytracerGenerators::class, 'nearNonZeroVectors')]
        Tuple $v,
        #[ForAllExternal(RaytracerGenerators::class, 'scalingFactors')]
        float $distance,
    ): void {
        $shape = Sphere::default();

        // The light is behind the surface by the given distance and is moved
        // parallel to the surface by a vector that is perpendicular to the
        // normal
        $light = PointLight::from(
            $point->minus($normal->multiplyBy(abs($distance)))->plus($normal->cross($v)),
            $intensity,
        );

        $this->assertEquals(
            $material->lighting($shape, $light, $point, $eye, $normal, true),
            $material->lighting($shape, $light, $point, $eye, $normal, false),
        );
    }

    #[Property]
    public function test_a_light_that_is_brighter_by_a_factor_lights_a_point_brighter_by_this_factor(
        Material $material,
        #[ForAllExternal(RaytracerGenerators::class, 'nearPoints')]
        Tuple $point,
        #[ForAllExternal(RaytracerGenerators::class, 'nearNonZeroVectors')]
        Tuple $fromPointToLight,
        #[ForAllExternal(RaytracerGenerators::class, 'displayableColors')]
        Color $intensity,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $eye,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $normal,
        #[ForAllExternal(RaytracerGenerators::class, 'scalingFactors')]
        float $factor,
        bool $inShadow,
    ): void {
        $shape    = Sphere::default();
        $light    = PointLight::from($point->plus($fromPointToLight), $intensity);
        $brighter = PointLight::from($point->plus($fromPointToLight), $intensity->multiplyBy(abs($factor)));

        $this->assertEqualsWithDelta(
            $material->lighting($shape, $light, $point, $eye, $normal, $inShadow)->multiplyBy(abs($factor)),
            $material->lighting($shape, $brighter, $point, $eye, $normal, $inShadow),
            0.000000001,
        );
    }

    #[Property]
    public function test_a_point_of_a_material_without_specular_reflection_is_lit_the_same_from_wherever_it_is_looked_at(
        Material $material,
        #[ForAllExternal(RaytracerGenerators::class, 'nearPoints')]
        Tuple $point,
        #[ForAllExternal(RaytracerGenerators::class, 'nearNonZeroVectors')]
        Tuple $fromPointToLight,
        #[ForAllExternal(RaytracerGenerators::class, 'displayableColors')]
        Color $intensity,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $eye,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $otherEye,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $normal,
    ): void {
        $shape = Sphere::default();
        $light = PointLight::from($point->plus($fromPointToLight), $intensity);

        $material->setSpecular(0.0);

        $this->assertEquals(
            $material->lighting($shape, $light, $point, $eye, $normal, false),
            $material->lighting($shape, $light, $point, $otherEye, $normal, false),
        );
    }
}
