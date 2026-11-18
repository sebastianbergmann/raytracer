<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function cos;
use function deg2rad;
use function sin;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UseGenerators;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RingPattern::class)]
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
final class RingPatternTest extends TestCase
{
    private Color $black;
    private Color $white;

    protected function setUp(): void
    {
        $this->black = Color::from(0, 0, 0);
        $this->white = Color::from(1, 1, 1);
    }

    public function test_a_ring_should_extend_in_both_x_and_z(): void
    {
        $pattern = Pattern::ring($this->white, $this->black);

        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 0))->equalTo($this->white));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(1, 0, 0))->equalTo($this->black));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0, 0, 1))->equalTo($this->black));
        $this->assertTrue($pattern->patternAt(Sphere::default(), Tuple::point(0.708, 0, 0.708))->equalTo($this->black));
    }

    /**
     * @param int<0, 32>     $ring
     * @param int<1, 7>      $eighths
     * @param int<-360, 360> $degrees
     */
    #[Property]
    public function test_rings_are_one_unit_wide_and_alternate_between_two_colors_with_the_distance_from_the_Y_axis(
        int $ring,
        int $eighths,
        int $degrees,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $y,
        Color $a,
        Color $b,
    ): void {
        $pattern = Pattern::ring($a, $b);
        $shape   = Sphere::default();
        $radius  = $ring + $eighths / 8;

        $at = static fn (float $radius): Tuple => Tuple::point(
            $radius * cos(deg2rad($degrees)),
            $y,
            $radius * sin(deg2rad($degrees)),
        );

        $color = $pattern->localPatternAt($shape, $at($radius));

        $this->assertSame($a, $pattern->localPatternAt($shape, $at($eighths / 8)));
        $this->assertSame($color, $pattern->localPatternAt($shape, Tuple::point($radius, 0, 0)));
        $this->assertSame($color, $pattern->localPatternAt($shape, Tuple::point(0, 0, -$radius)));
        $this->assertNotSame($color, $pattern->localPatternAt($shape, $at($radius + 1)));
        $this->assertSame($color, $pattern->localPatternAt($shape, $at($radius + 2)));
    }

    #[Property]
    #[TestWith([1e30, 1e30, 1e30], 'coordinates that are too large for an integer')]
    #[TestWith([-1e30, -1e30, -1e30], 'coordinates that are too small for an integer')]
    public function test_a_ring_pattern_has_one_of_its_two_colors_at_any_point(float $x, float $y, float $z): void
    {
        $pattern = Pattern::ring($this->white, $this->black);

        $this->assertContains(
            $pattern->localPatternAt(Sphere::default(), Tuple::point($x, $y, $z)),
            [$this->white, $this->black],
        );
    }
}
