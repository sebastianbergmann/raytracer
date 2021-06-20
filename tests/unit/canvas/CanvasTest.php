<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function iterator_to_array;
use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Canvas
 * @covers \SebastianBergmann\Raytracer\CanvasIterator
 *
 * @uses \SebastianBergmann\Raytracer\Color
 *
 * @small
 */
final class CanvasTest extends TestCase
{
    public function test_has_width_and_height(): void
    {
        $canvas = Canvas::create(10, 20, Color::create(0.0, 0.0, 0.0));

        $this->assertSame(10, $canvas->width());
        $this->assertSame(20, $canvas->height());
    }

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

    public function test_can_be_iterated(): void
    {
        $black  = Color::create(0.0, 0.0, 0.0);
        $canvas = Canvas::create(2, 2, $black);
        $pixels = iterator_to_array($canvas);

        $this->assertCount(4, $pixels);

        foreach ($pixels as $pixel) {
            $this->assertTrue($pixel->equalTo($black));
        }
    }
}
