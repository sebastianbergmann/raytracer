<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GradientPattern::class)]
#[CoversClass(Pattern::class)]
#[UsesClass(Color::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Shape::class)]
#[UsesClass(Sphere::class)]
#[UsesClass(Tuple::class)]
#[Group('material')]
#[Small]
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
