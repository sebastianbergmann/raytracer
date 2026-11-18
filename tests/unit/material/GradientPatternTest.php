<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function max;
use function min;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UseGenerators;
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
#[UseGenerators(RaytracerGenerators::class)]
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

    /**
     * @param int<-64, 64> $x
     * @param int<0, 7>    $eighths
     */
    #[Property]
    public function test_a_gradient_blends_from_one_color_to_another_within_one_unit_along_the_X_axis(
        int $x,
        int $eighths,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $y,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $z,
        Color $a,
        Color $b,
    ): void {
        $pattern = Pattern::gradient($a, $b);
        $shape   = Sphere::default();

        $color = $pattern->localPatternAt($shape, Tuple::point($x + $eighths / 8, $y, $z));

        $this->assertEquals($a, $pattern->localPatternAt($shape, Tuple::point((float) $x, $y, $z)));
        $this->assertEquals($a->plus($b)->multiplyBy(0.5), $pattern->localPatternAt($shape, Tuple::point($x + 0.5, $y, $z)));
        $this->assertEquals($color, $pattern->localPatternAt($shape, Tuple::point($eighths / 8, 0, 0)));

        $this->assertGreaterThanOrEqual(min($a->red(), $b->red()), $color->red());
        $this->assertLessThanOrEqual(max($a->red(), $b->red()), $color->red());
        $this->assertGreaterThanOrEqual(min($a->green(), $b->green()), $color->green());
        $this->assertLessThanOrEqual(max($a->green(), $b->green()), $color->green());
        $this->assertGreaterThanOrEqual(min($a->blue(), $b->blue()), $color->blue());
        $this->assertLessThanOrEqual(max($a->blue(), $b->blue()), $color->blue());
    }
}
