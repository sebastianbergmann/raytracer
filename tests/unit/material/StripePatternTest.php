<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UseGenerators;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(StripePattern::class)]
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
final class StripePatternTest extends TestCase
{
    private Color $black;
    private Color $white;

    protected function setUp(): void
    {
        $this->black = Color::from(0, 0, 0);
        $this->white = Color::from(1, 1, 1);
    }

    public function test_a_stripe_pattern_is_constant_in_y(): void
    {
        $pattern = Pattern::stripe($this->white, $this->black);

        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 1, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 2, 0))->equalTo($this->white));
    }

    public function test_a_stripe_pattern_is_constant_in_z(): void
    {
        $pattern = Pattern::stripe($this->white, $this->black);

        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 1))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 2))->equalTo($this->white));
    }

    public function test_a_stripe_pattern_alternates_in_x(): void
    {
        $pattern = Pattern::stripe($this->white, $this->black);

        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0.9, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(1, 0, 0))->equalTo($this->black));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(-0.1, 0, 0))->equalTo($this->black));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(-1, 0, 0))->equalTo($this->black));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(-1.1, 0, 0))->equalTo($this->white));
    }

    /**
     * @param int<-64, 64> $stripe
     * @param int<0, 7>    $eighths
     */
    #[Property]
    public function test_stripes_are_one_unit_wide_and_alternate_between_two_colors_along_the_X_axis(
        int $stripe,
        int $eighths,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $y,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $z,
        Color $a,
        Color $b,
    ): void {
        $pattern = Pattern::stripe($a, $b);
        $shape   = Sphere::default();
        $x       = $stripe + $eighths / 8;

        $color = $pattern->localPatternAt($shape, Tuple::point($x, $y, $z));

        $this->assertSame($a, $pattern->localPatternAt($shape, Tuple::point($eighths / 8, $y, $z)));
        $this->assertSame($color, $pattern->localPatternAt($shape, Tuple::point($x, 0, 0)));
        $this->assertSame($color, $pattern->localPatternAt($shape, Tuple::point((float) $stripe, $y, $z)));
        $this->assertNotSame($color, $pattern->localPatternAt($shape, Tuple::point($x + 1, $y, $z)));
        $this->assertSame($color, $pattern->localPatternAt($shape, Tuple::point($x + 2, $y, $z)));
    }

    #[Property]
    #[TestWith([1e30, 1e30, 1e30], 'coordinates that are too large for an integer')]
    #[TestWith([-1e30, -1e30, -1e30], 'coordinates that are too small for an integer')]
    public function test_a_stripe_pattern_has_one_of_its_two_colors_at_any_point(float $x, float $y, float $z): void
    {
        $pattern = Pattern::stripe($this->white, $this->black);

        $this->assertContains(
            $pattern->localPatternAt(Sphere::default(), Tuple::point($x, $y, $z)),
            [$this->white, $this->black],
        );
    }
}
