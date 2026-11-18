<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function iterator_to_array;
use function range;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UseGenerators;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Canvas::class)]
#[CoversClass(CanvasIterator::class)]
#[UsesClass(Color::class)]
#[UseGenerators(RaytracerGenerators::class)]
#[Group('canvas')]
#[Small]
final class CanvasTest extends TestCase
{
    public function test_has_width_and_height(): void
    {
        $canvas = Canvas::from(10, 20, Color::from(0.0, 0.0, 0.0));

        $this->assertSame(10, $canvas->width());
        $this->assertSame(20, $canvas->height());
    }

    public function test_is_initially_black(): void
    {
        $canvas = Canvas::from(10, 20, Color::from(0.0, 0.0, 0.0));
        $black  = Color::from(0.0, 0.0, 0.0);

        foreach (range(1, 10) as $x) {
            foreach (range(1, 20) as $y) {
                $this->assertTrue($canvas->pixelAt($x, $y)->equalTo($black));
            }
        }
    }

    public function test_a_pixel_can_be_written_to_a_canvas(): void
    {
        $canvas = Canvas::from(10, 20, Color::from(0.0, 0.0, 0.0));
        $red    = Color::from(1.0, 0.0, 0.0);

        $canvas->writePixel(2, 3, $red);

        $this->assertTrue($canvas->pixelAt(2, 3)->equalTo($red));
    }

    public function test_can_be_iterated(): void
    {
        $black  = Color::from(0.0, 0.0, 0.0);
        $canvas = Canvas::from(2, 2, $black);
        $pixels = iterator_to_array($canvas);

        $this->assertCount(4, $pixels);

        foreach ($pixels as $pixel) {
            $this->assertTrue($pixel->equalTo($black));
        }
    }

    /**
     * @param int<1, 8> $width
     * @param int<1, 8> $height
     */
    #[Property]
    public function test_a_new_canvas_has_its_background_color_everywhere(int $width, int $height, Color $background): void
    {
        $canvas = Canvas::from($width, $height, $background);

        $this->assertSame($width, $canvas->width());
        $this->assertSame($height, $canvas->height());

        for ($x = 1; $x <= $width; $x++) {
            for ($y = 1; $y <= $height; $y++) {
                $this->assertSame($background, $canvas->pixelAt($x, $y));
            }
        }
    }

    #[Property]
    public function test_a_canvas_is_iterated_row_by_row_from_left_to_right(Canvas $canvas): void
    {
        $pixels = iterator_to_array($canvas);
        $key    = 0;

        for ($y = 1; $y <= $canvas->height(); $y++) {
            for ($x = 1; $x <= $canvas->width(); $x++) {
                $key++;

                $this->assertArrayHasKey($key, $pixels);
                $this->assertSame($canvas->pixelAt($x, $y), $pixels[$key]);
            }
        }

        $this->assertCount($key, $pixels);
    }

    #[Property]
    public function test_each_pixel_has_the_color_that_was_written_to_it_last(Color $background): void
    {
        $this->runStateMachine(new CanvasMachine($background));
    }
}
