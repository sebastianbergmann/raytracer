<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Pattern
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Shape
 * @uses \SebastianBergmann\Raytracer\Sphere
 * @uses \SebastianBergmann\Raytracer\Transformations
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
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
}
