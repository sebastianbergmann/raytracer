<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const PHP_EOL;
use const PREG_SET_ORDER;
use function array_map;
use function array_slice;
use function intdiv;
use function preg_match_all;
use function substr_count;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UseGenerators;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AnsiMapper::class)]
#[UsesClass(Canvas::class)]
#[UsesClass(Color::class)]
#[UseGenerators(RaytracerGenerators::class)]
#[Group('canvas')]
#[Small]
final class AnsiMapperTest extends TestCase
{
    public function test_can_map_Canvas_to_ANSI_string(): void
    {
        $canvas = Canvas::from(10, 20, Color::from(0.0, 0.0, 0.0));

        $this->assertStringEqualsFile(
            __DIR__ . '/../../fixture/10_20_empty.ansi',
            (new AnsiMapper)->map($canvas),
        );
    }

    public function test_can_map_Canvas_with_an_odd_number_of_rows_to_ANSI_string(): void
    {
        $canvas = Canvas::from(1, 1, Color::from(1.0, 1.0, 1.0));

        $this->assertSame(
            "\x1b[2J\x1b[H\x1b[48;2;255;255;255m\x1b[38;2;0;0;0m▄" . PHP_EOL . "\x1b[0m",
            (new AnsiMapper)->map($canvas),
        );
    }

    #[Property]
    public function test_an_ANSI_string_has_one_character_for_every_two_pixels_of_a_column(Canvas $canvas): void
    {
        $ansi         = (new AnsiMapper)->map($canvas);
        $numberOfRows = intdiv($canvas->height() + 1, 2);

        $this->assertStringStartsWith("\x1b[2J\x1b[H", $ansi);
        $this->assertStringEndsWith("\x1b[0m", $ansi);
        $this->assertSame($numberOfRows, substr_count($ansi, PHP_EOL));
        $this->assertSame($numberOfRows * $canvas->width(), substr_count($ansi, '▄'));
    }

    #[Property]
    public function test_the_pixels_of_a_canvas_can_be_read_from_its_ANSI_string(Canvas $canvas): void
    {
        $ansi = (new AnsiMapper)->map($canvas);

        preg_match_all('/\x1b\[48;2;(\d+);(\d+);(\d+)m\x1b\[38;2;(\d+);(\d+);(\d+)m▄/', $ansi, $characters, PREG_SET_ORDER);

        $expected = [];

        for ($y = 1; $y <= $canvas->height(); $y += 2) {
            for ($x = 1; $x <= $canvas->width(); $x++) {
                $upper = $canvas->pixelAt($x, $y);
                $lower = $y < $canvas->height() ? $canvas->pixelAt($x, $y + 1) : Color::from(0, 0, 0);

                $expected[] = [
                    (string) $upper->redAsInt(),
                    (string) $upper->greenAsInt(),
                    (string) $upper->blueAsInt(),
                    (string) $lower->redAsInt(),
                    (string) $lower->greenAsInt(),
                    (string) $lower->blueAsInt(),
                ];
            }
        }

        $this->assertSame(
            $expected,
            array_map(
                static fn (array $character): array => array_slice($character, 1),
                $characters,
            ),
        );
    }
}
