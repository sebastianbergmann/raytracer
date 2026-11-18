<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function abs;
use function max;
use function min;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAll;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\UseGenerators;
use PHPUnit\Framework\PropertyBased\Generators;
use PHPUnit\Framework\PropertyBased\ValueGenerator;
use PHPUnit\Framework\TestCase;

#[CoversClass(Color::class)]
#[UseGenerators(RaytracerGenerators::class)]
#[Small]
final class ColorTest extends TestCase
{
    /**
     * @return ValueGenerator<float>
     */
    public static function displayableChannels(): ValueGenerator
    {
        return Generators::floats(0.0, 1.0);
    }

    #[TestDox('Colors are (red, green, blue) tuples')]
    public function test_colors_are_red_green_blue_tuples(): void
    {
        $c = Color::from(-0.5, 0.4, 1.7);

        $this->assertSame(-0.5, $c->red());
        $this->assertSame(0.4, $c->green());
        $this->assertSame(1.7, $c->blue());
    }

    public function test_float_components_can_be_represented_as_integers(): void
    {
        $c = Color::from(-0.9, 0.5, 1.1);

        $this->assertSame(0, $c->redAsInt());
        $this->assertSame(127, $c->greenAsInt());
        $this->assertSame(255, $c->blueAsInt());
    }

    public function test_another_color_can_be_added(): void
    {
        $c1 = Color::from(0.9, 0.6, 0.75);
        $c2 = Color::from(0.7, 0.1, 0.25);

        $c3 = $c1->plus($c2);

        $this->assertSame(1.6, $c3->red());
        $this->assertSame(0.7, $c3->green());
        $this->assertSame(1.0, $c3->blue());
    }

    public function test_another_color_can_be_subtracted(): void
    {
        $c1 = Color::from(0.9, 0.6, 0.75);
        $c2 = Color::from(0.7, 0.1, 0.25);

        $c3 = $c1->minus($c2);

        $this->assertEqualsWithDelta(0.2, $c3->red(), 0.0000000000000001);
        $this->assertEqualsWithDelta(0.5, $c3->green(), 0.0000000000000001);
        $this->assertEqualsWithDelta(0.5, $c3->blue(), 0.0000000000000001);
    }

    public function test_can_be_multiplied_by_a_scalar(): void
    {
        $c1 = Color::from(0.2, 0.3, 0.4);

        $c2 = $c1->multiplyBy(2.0);

        $this->assertSame(0.4, $c2->red());
        $this->assertSame(0.6, $c2->green());
        $this->assertSame(0.8, $c2->blue());
    }

    public function test_the_product_of_two_colors_can_be_calculated(): void
    {
        $c1 = Color::from(1.0, 0.2, 0.4);
        $c2 = Color::from(0.9, 1.0, 0.1);

        $c3 = $c1->product($c2);

        $this->assertEqualsWithDelta(0.9, $c3->red(), 0.00000000000000001);
        $this->assertEqualsWithDelta(0.2, $c3->green(), 0.00000000000000001);
        $this->assertEqualsWithDelta(0.04, $c3->blue(), 0.00000000000000001);
    }

    public function test_can_be_compared_to_another_color(): void
    {
        $c1 = Color::from(0.0, 0.0, 0.0);
        $c2 = Color::from(1.0, 0.0, 0.0);
        $c3 = Color::from(0.0, 1.0, 0.0);
        $c4 = Color::from(0.0, 0.0, 1.0);

        $this->assertTrue($c1->equalTo($c1));
        $this->assertFalse($c1->equalTo($c2));
        $this->assertFalse($c1->equalTo($c3));
        $this->assertFalse($c1->equalTo($c4));
    }

    #[Property]
    public function test_addition_is_commutative_and_associative_and_undone_by_subtraction(Color $a, Color $b, Color $c): void
    {
        $this->assertEquals($a->plus($b), $b->plus($a));
        $this->assertEquals($a->plus($b)->plus($c), $a->plus($b->plus($c)));
        $this->assertEquals($a, $a->plus($b)->minus($b));
    }

    #[Property]
    public function test_the_product_is_commutative_and_associative(Color $a, Color $b, Color $c): void
    {
        $this->assertEquals($a->product($b), $b->product($a));
        $this->assertEquals($a->product($b)->product($c), $a->product($b->product($c)));
    }

    #[Property]
    public function test_the_product_with_white_is_the_color_and_the_product_with_black_is_black(Color $color): void
    {
        $white = Color::from(1, 1, 1);
        $black = Color::from(0, 0, 0);

        $this->assertEquals($color, $color->product($white));
        $this->assertEquals($black, $color->product($black));
    }

    #[Property]
    public function test_multiplication_by_a_scalar_distributes_over_addition(
        Color $a,
        Color $b,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $factor,
    ): void {
        $this->assertEquals(
            $a->plus($b)->multiplyBy($factor),
            $a->multiplyBy($factor)->plus($b->multiplyBy($factor)),
        );
    }

    /**
     * @param int<0, 2> $channel
     */
    #[Property]
    public function test_colors_are_equal_exactly_when_none_of_their_channels_differ(
        Color $color,
        int $channel,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroCoordinates')]
        float $difference,
    ): void {
        $channels = [$color->red(), $color->green(), $color->blue()];

        $same = Color::from(...$channels);

        $channels[$channel] += $difference;

        $other = Color::from(...$channels);

        $this->assertTrue($color->equalTo($same));
        $this->assertTrue($same->equalTo($color));
        $this->assertFalse($color->equalTo($other));
        $this->assertFalse($other->equalTo($color));
    }

    #[Property]
    public function test_colors_are_equal_when_their_channels_differ_by_no_more_than_the_delta(
        Color $color,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroCoordinates')]
        float $difference,
    ): void {
        $delta = abs($difference);

        foreach ([[1, 0, 0], [0, 1, 0], [0, 0, 1]] as [$red, $green, $blue]) {
            $other = Color::from(
                $color->red() + $red * $difference,
                $color->green() + $green * $difference,
                $color->blue() + $blue * $difference,
            );

            $this->assertTrue($color->equalTo($other, $delta));
            $this->assertTrue($other->equalTo($color, $delta));
            $this->assertFalse($color->equalTo($other, $delta / 2));
            $this->assertFalse($other->equalTo($color, $delta / 2));
        }
    }

    #[Property]
    #[TestWith([1e300, -1e300, 1e17], 'channels that are too large for an integer')]
    public function test_channels_of_any_finite_floats_are_represented_as_integers_from_0_to_255(float $red, float $green, float $blue): void
    {
        $color = Color::from($red, $green, $blue);

        foreach ([$color->redAsInt(), $color->greenAsInt(), $color->blueAsInt()] as $channel) {
            $this->assertGreaterThanOrEqual(0, $channel);
            $this->assertLessThanOrEqual(255, $channel);
        }
    }

    #[Property]
    #[TestWith([0.0, 1.0], 'bounds of the displayable range')]
    #[TestWith([-1e300, 1e300], 'far outside of the displayable range')]
    public function test_a_brighter_channel_is_never_represented_by_a_smaller_integer(float $a, float $b): void
    {
        $darker   = Color::from(min($a, $b), min($a, $b), min($a, $b));
        $brighter = Color::from(max($a, $b), max($a, $b), max($a, $b));

        $this->assertLessThanOrEqual($brighter->redAsInt(), $darker->redAsInt());
        $this->assertLessThanOrEqual($brighter->greenAsInt(), $darker->greenAsInt());
        $this->assertLessThanOrEqual($brighter->blueAsInt(), $darker->blueAsInt());
    }

    #[Property]
    #[TestWith([0.0])]
    #[TestWith([1.0])]
    public function test_a_displayable_channel_loses_less_than_one_step_when_it_is_represented_as_an_integer(
        #[ForAll('displayableChannels')]
        float $channel,
    ): void {
        $step = Color::from($channel, $channel, $channel)->redAsInt();

        $this->assertLessThanOrEqual($channel + 0.000000000000001, $step / 255);

        if ($step < 255) {
            $this->assertGreaterThan($channel - 0.000000000000001, ($step + 1) / 255);
        } else {
            $this->assertEqualsWithDelta(1.0, $channel, 0.0);
        }
    }
}
