<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const PHP_EOL;
use function array_slice;
use function explode;
use function preg_split;
use function strlen;
use function trim;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UseGenerators;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PortablePixmapMapper::class)]
#[UsesClass(Canvas::class)]
#[UsesClass(CanvasIterator::class)]
#[UsesClass(Color::class)]
#[UseGenerators(RaytracerGenerators::class)]
#[Group('canvas')]
#[Small]
final class PortablePixmapMapperTest extends TestCase
{
    public function test_can_map_Canvas_to_PPM_string(): void
    {
        $canvas = Canvas::from(10, 20, Color::from(0.0, 0.0, 0.0));

        $this->assertStringEqualsFile(
            __DIR__ . '/../../fixture/10_20_empty.ppm',
            (new PortablePixmapMapper)->map($canvas),
        );
    }

    #[Property]
    public function test_the_size_and_the_pixels_of_a_canvas_can_be_read_from_its_PPM_string(Canvas $canvas): void
    {
        $tokens = preg_split('/\s+/', trim((new PortablePixmapMapper)->map($canvas)));

        $this->assertIsArray($tokens);

        $expected = [];

        for ($y = 1; $y <= $canvas->height(); $y++) {
            for ($x = 1; $x <= $canvas->width(); $x++) {
                $pixel = $canvas->pixelAt($x, $y);

                $expected[] = (string) $pixel->redAsInt();
                $expected[] = (string) $pixel->greenAsInt();
                $expected[] = (string) $pixel->blueAsInt();
            }
        }

        $this->assertSame(['P3', (string) $canvas->width(), (string) $canvas->height(), '255'], array_slice($tokens, 0, 4));
        $this->assertSame($expected, array_slice($tokens, 4));
    }

    #[Property]
    public function test_no_line_of_a_PPM_string_is_longer_than_70_characters(Canvas $canvas): void
    {
        $ppm = (new PortablePixmapMapper)->map($canvas);

        $this->assertStringEndsWith(PHP_EOL, $ppm);

        foreach (explode(PHP_EOL, $ppm) as $line) {
            $this->assertLessThanOrEqual(70, strlen($line));
        }
    }
}
