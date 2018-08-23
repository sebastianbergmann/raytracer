<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Color
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
}
