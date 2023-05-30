<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PortablePixmapMapper::class)]
#[UsesClass(Canvas::class)]
#[UsesClass(CanvasIterator::class)]
#[UsesClass(Color::class)]
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
}
