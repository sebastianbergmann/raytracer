<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UseGenerators;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CheckersPattern::class)]
#[CoversClass(Pattern::class)]
#[UsesClass(Color::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Shape::class)]
#[UsesClass(Sphere::class)]
#[UsesClass(Tuple::class)]
#[UseGenerators(RaytracerGenerators::class)]
#[Group('material')]
#[Small]
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

    #[Property]
    #[TestWith([1e30, 1e30, 1e30], 'coordinates that are too large for an integer')]
    #[TestWith([-1e30, -1e30, -1e30], 'coordinates that are too small for an integer')]
    public function test_a_checkers_pattern_has_one_of_its_two_colors_at_any_point(float $x, float $y, float $z): void
    {
        $pattern = Pattern::checkers($this->white, $this->black);

        $this->assertContains(
            $pattern->localPatternAt(Sphere::default(), Tuple::point($x, $y, $z)),
            [$this->white, $this->black],
        );
    }
}
