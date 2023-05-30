<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AnsiMapper::class)]
#[UsesClass(Canvas::class)]
#[UsesClass(Color::class)]
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
}
