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
        $canvas = Canvas::create(10, 20, Color::create(0.0, 0.0, 0.0));
        $black  = Color::create(0.0, 0.0, 0.0);

        for ($x = 1; $x <= 10; $x++) {
            for ($y = 1; $y <= 20; $y++) {
                $this->assertTrue($canvas->pixelAt($x, $y)->equalTo($black));
            }
        }
    }

    public function test_a_pixel_can_be_written_to_a_canvas(): void
    {
        $canvas = Canvas::create(10, 20, Color::create(0.0, 0.0, 0.0));
        $red    = Color::create(1.0, 0.0, 0.0);

        $canvas->writePixel(2, 3, $red);

        $this->assertTrue($canvas->pixelAt(2, 3)->equalTo($red));
    }
}
