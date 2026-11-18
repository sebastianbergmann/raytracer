<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const PHP_EOL;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
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
}
