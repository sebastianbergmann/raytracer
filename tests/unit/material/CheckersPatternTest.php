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

    /**
     * @param int<-64, 64> $cellX
     * @param int<-64, 64> $cellY
     * @param int<-64, 64> $cellZ
     * @param int<0, 7>    $eighthsX
     * @param int<0, 7>    $eighthsY
     * @param int<0, 7>    $eighthsZ
     */
    #[Property]
    public function test_checkers_are_cubes_with_edges_of_one_unit_that_have_another_color_than_the_cubes_next_to_them(
        int $cellX,
        int $cellY,
        int $cellZ,
        int $eighthsX,
        int $eighthsY,
        int $eighthsZ,
        Color $a,
        Color $b,
    ): void {
        $pattern = Pattern::checkers($a, $b);
        $shape   = Sphere::default();
        $x       = $cellX + $eighthsX / 8;
        $y       = $cellY + $eighthsY / 8;
        $z       = $cellZ + $eighthsZ / 8;

        $color = $pattern->localPatternAt($shape, Tuple::point($x, $y, $z));

        $this->assertSame($a, $pattern->localPatternAt($shape, Tuple::point($eighthsX / 8, $eighthsY / 8, $eighthsZ / 8)));
        $this->assertSame($color, $pattern->localPatternAt($shape, Tuple::point((float) $cellX, (float) $cellY, (float) $cellZ)));

        $this->assertNotSame($color, $pattern->localPatternAt($shape, Tuple::point($x + 1, $y, $z)));
        $this->assertNotSame($color, $pattern->localPatternAt($shape, Tuple::point($x, $y + 1, $z)));
        $this->assertNotSame($color, $pattern->localPatternAt($shape, Tuple::point($x, $y, $z + 1)));

        $this->assertSame($color, $pattern->localPatternAt($shape, Tuple::point($x + 1, $y + 1, $z)));
        $this->assertSame($color, $pattern->localPatternAt($shape, Tuple::point($x, $y + 1, $z + 1)));
        $this->assertSame($color, $pattern->localPatternAt($shape, Tuple::point($x + 1, $y, $z + 1)));
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
