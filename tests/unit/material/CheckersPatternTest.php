<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\CheckersPattern
 * @covers \SebastianBergmann\Raytracer\Pattern
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Shape
 * @uses \SebastianBergmann\Raytracer\Sphere
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class CheckersPatternTest extends TestCase
{
    private Color $black;
    private Color $white;

    protected function setUp(): void
    {
        $this->black = Color::from(0, 0, 0);
        $this->white = Color::from(1, 1, 1);
    }

    public function test_checkers_should_repeat_in_x(): void
    {
        $pattern = Pattern::checkers($this->white, $this->black);

        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0.99, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(1.01, 0, 0))->equalTo($this->black));
    }

    public function test_checkers_should_repeat_in_y(): void
    {
        $pattern = Pattern::checkers($this->white, $this->black);

        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0.99, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 1.01, 0))->equalTo($this->black));
    }

    public function test_checkers_should_repeat_in_z(): void
    {
        $pattern = Pattern::checkers($this->white, $this->black);

        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 0.99))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 1.01))->equalTo($this->black));
    }
}
