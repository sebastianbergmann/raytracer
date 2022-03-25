<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\GradientPattern
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
final class GradientPatternTest extends TestCase
{
    private Color $black;
    private Color $white;

    protected function setUp(): void
    {
        $this->black = Color::from(0, 0, 0);
        $this->white = Color::from(1, 1, 1);
    }

    public function test_a_gradient_linearly_interpolates_between_colors(): void
    {
        $pattern = Pattern::gradient($this->white, $this->black);

        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 0))->equalTo(Color::from(1, 1, 1)));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0.25, 0, 0))->equalTo(Color::from(0.75, 0.75, 0.75)));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0.5, 0, 0))->equalTo(Color::from(0.5, 0.5, 0.5)));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0.75, 0, 0))->equalTo(Color::from(0.25, 0.25, 0.25)));
    }
}
