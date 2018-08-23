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
}
