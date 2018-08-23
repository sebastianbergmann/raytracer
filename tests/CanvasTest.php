<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Canvas
 *
 * @uses \SebastianBergmann\Raytracer\Color
 */
final class CanvasTest extends TestCase
{
    public function test_is_initially_black(): void
    {
        $c = Canvas::create(10, 20, Color::create(0.0, 0.0, 0.0));

        for ($x = 1; $x <= 10; $x++) {
            for ($y = 1; $y <= 20; $y++) {
                $this->assertSame(0.0, $c->pixelAt($x, $y)->red());
                $this->assertSame(0.0, $c->pixelAt($x, $y)->green());
                $this->assertSame(0.0, $c->pixelAt($x, $y)->blue());
            }
        }
    }

    public function test_a_pixel_can_be_written_to_a_canvas(): void
    {
        $c = Canvas::create(10, 20, Color::create(0.0, 0.0, 0.0));

        $c->writePixel(2, 3, Color::create(1.0, 0.0, 0.0));

        $this->assertSame(1.0, $c->pixelAt(2, 3)->red());
        $this->assertSame(0.0, $c->pixelAt(2, 3)->green());
        $this->assertSame(0.0, $c->pixelAt(2, 3)->blue());
    }
}
