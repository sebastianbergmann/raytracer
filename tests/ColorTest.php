<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Color
 *
 * @small
 */
final class ColorTest extends TestCase
{
    /**
     * @testdox Colors are (red, green, blue) tuples
     */
    public function test_colors_are_red_green_blue_tuples(): void
    {
        $c = Color::create(-0.5, 0.4, 1.7);

        $this->assertSame(-0.5, $c->red());
        $this->assertSame(0.4, $c->green());
        $this->assertSame(1.7, $c->blue());
    }

    public function test_another_color_can_be_added(): void
    {
        $c1 = Color::create(0.9, 0.6, 0.75);
        $c2 = Color::create(0.7, 0.1, 0.25);

        $c3 = $c1->plus($c2);

        $this->assertSame(1.6, $c3->red());
        $this->assertSame(0.7, $c3->green());
        $this->assertSame(1.0, $c3->blue());
    }

    public function test_another_color_can_be_subtracted(): void
    {
        $c1 = Color::create(0.9, 0.6, 0.75);
        $c2 = Color::create(0.7, 0.1, 0.25);

        $c3 = $c1->minus($c2);

        $this->assertSame(0.2, $c3->red());
        $this->assertSame(0.5, $c3->green());
        $this->assertSame(0.5, $c3->blue());
    }

    public function test_can_be_multiplied_by_a_scalar(): void
    {
        $c1 = Color::create(0.2, 0.3, 0.4);

        $c2 = $c1->multiplyBy(2.0);

        $this->assertSame(0.4, $c2->red());
        $this->assertSame(0.6, $c2->green());
        $this->assertSame(0.8, $c2->blue());
    }

    public function test_the_product_of_two_colors_can_be_calculated(): void
    {
        $c1 = Color::create(1.0, 0.2, 0.4);
        $c2 = Color::create(0.9, 1.0, 0.1);

        $c3 = $c1->product($c2);

        $this->assertSame(0.9, $c3->red());
        $this->assertSame(0.2, $c3->green());
        $this->assertSame(0.04, $c3->blue());
    }

    public function test_can_be_compared_to_another_color(): void
    {
        $c1 = Color::create(0.0, 0.0, 0.0);
        $c2 = Color::create(1.0, 1.0, 1.0);

        $this->assertTrue($c1->equalTo($c1));
        $this->assertFalse($c1->equalTo($c2));
    }
}
