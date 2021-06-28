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
    private Color $black;

    private Color $white;

    protected function setUp(): void
    {
        $this->black = Color::from(0, 0, 0);
        $this->white = Color::from(1, 1, 1);
    }

    public function test_creating_a_stripe_pattern(): void
    {
        $pattern = Pattern::from($this->black, $this->white);

        $this->assertTrue($pattern->a()->equalTo($this->black));
        $this->assertTrue($pattern->b()->equalTo($this->white));
    }

    public function test_a_stripe_pattern_is_constant_in_y(): void
    {
        $pattern = Pattern::from($this->white, $this->black);

        $this->assertTrue($pattern->stripeAt(Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->stripeAt(Tuple::point(0, 1, 0))->equalTo($this->white));
        $this->assertTrue($pattern->stripeAt(Tuple::point(0, 2, 0))->equalTo($this->white));
    }

    public function test_a_stripe_pattern_is_constant_in_z(): void
    {
        $pattern = Pattern::from($this->white, $this->black);

        $this->assertTrue($pattern->stripeAt(Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->stripeAt(Tuple::point(0, 0, 1))->equalTo($this->white));
        $this->assertTrue($pattern->stripeAt(Tuple::point(0, 0, 2))->equalTo($this->white));
    }

    public function test_a_stripe_pattern_alternates_in_x(): void
    {
        $pattern = Pattern::from($this->white, $this->black);

        $this->assertTrue($pattern->stripeAt(Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->stripeAt(Tuple::point(0.9, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->stripeAt(Tuple::point(1, 0, 0))->equalTo($this->black));
        $this->assertTrue($pattern->stripeAt(Tuple::point(-0.1, 0, 0))->equalTo($this->black));
        $this->assertTrue($pattern->stripeAt(Tuple::point(-1, 0, 0))->equalTo($this->black));
        $this->assertTrue($pattern->stripeAt(Tuple::point(-1.1, 0, 0))->equalTo($this->white));
    }

    public function test_stripes_with_an_object_transformation(): void
    {
        $object = Sphere::default();
        $object->setTransform(Transformations::scaling(2, 2, 2));

        $pattern = Pattern::from($this->white, $this->black);

        $this->assertTrue($pattern->stripeAtObject($object, Tuple::point(1.5, 0, 0))->equalTo($this->white));
    }

    public function test_stripes_with_a_pattern_transformation(): void
    {
        $object = Sphere::default();

        $pattern = Pattern::from($this->white, $this->black);
        $pattern->setTransform(Transformations::scaling(2, 2, 2));

        $this->assertTrue($pattern->stripeAtObject($object, Tuple::point(1.5, 0, 0))->equalTo($this->white));
    }

    public function test_stripes_with_both_an_object_and_a_pattern_transformation(): void
    {
        $object = Sphere::default();
        $object->setTransform(Transformations::scaling(2, 2, 2));

        $pattern = Pattern::from($this->white, $this->black);
        $pattern->setTransform(Transformations::translation(0.5, 0, 0));

        $this->assertTrue($pattern->stripeAtObject($object, Tuple::point(2.5, 0, 0))->equalTo($this->white));
    }
}
