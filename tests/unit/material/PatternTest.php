<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Pattern::class)]
#[UsesClass(Color::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Shape::class)]
#[UsesClass(Sphere::class)]
#[UsesClass(Transformations::class)]
#[UsesClass(Tuple::class)]
#[Group('material')]
#[Small]
final class PatternTest extends TestCase
{
    public function test_the_default_pattern_transformation(): void
    {
        $pattern = TestPattern::default();

        $this->assertTrue($pattern->transform()->equalTo(Matrix::identity(4)));
    }

    public function test_assigning_a_transformation(): void
    {
        $pattern        = TestPattern::default();
        $transformation = Transformations::translation(1, 2, 3);

        $pattern->setTransform($transformation);

        $this->assertSame($transformation, $pattern->transform());
    }

    public function test_a_pattern_with_an_object_transformation(): void
    {
        $object = Sphere::default();
        $object->setTransform(Transformations::scaling(2, 2, 2));

        $pattern = TestPattern::default();

        $this->assertTrue($pattern->patternAt($object, Tuple::point(2, 3, 4))->equalTo(Color::from(1, 1.5, 2)));
    }

    public function test_a_pattern_with_a_pattern_transformation(): void
    {
        $object = Sphere::default();

        $pattern = TestPattern::default();
        $pattern->setTransform(Transformations::scaling(2, 2, 2));

        $this->assertTrue($pattern->patternAt($object, Tuple::point(2, 3, 4))->equalTo(Color::from(1, 1.5, 2)));
    }

    public function test_a_pattern_with_both_an_object_and_a_pattern_transformation(): void
    {
        $object = Sphere::default();
        $object->setTransform(Transformations::scaling(2, 2, 2));

        $pattern = TestPattern::default();
        $pattern->setTransform(Transformations::translation(0.5, 1, 1.5));

        $this->assertTrue($pattern->patternAt($object, Tuple::point(2.5, 3, 3.5))->equalTo(Color::from(0.75, 0.5, 0.25)));
    }

    #[Property]
    public function test_a_pattern_is_evaluated_at_the_point_that_is_transformed_back_from_world_space_to_the_space_of_the_pattern(
        #[ForAllExternal(RaytracerGenerators::class, 'nearPoints')]
        Tuple $point,
        #[ForAllExternal(RaytracerGenerators::class, 'transformations')]
        Matrix $transformationOfShape,
        #[ForAllExternal(RaytracerGenerators::class, 'transformations')]
        Matrix $transformationOfPattern,
    ): void {
        $shape   = Sphere::default();
        $pattern = TestPattern::default();

        $shape->setTransform($transformationOfShape);
        $pattern->setTransform($transformationOfPattern);

        $pointInWorldSpace = $transformationOfShape->multiply($transformationOfPattern)->multiplyBy($point);

        $this->assertEqualsWithDelta(
            Color::from($point->x, $point->y, $point->z),
            $pattern->patternAt($shape, $pointInWorldSpace),
            0.0000001,
        );
    }
}
