<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\PortablePixmapMapper
 *
 * @uses \SebastianBergmann\Raytracer\Canvas
 * @uses \SebastianBergmann\Raytracer\CanvasIterator
 * @uses \SebastianBergmann\Raytracer\Color
 */
final class PortablePixmapMapperTest extends TestCase
{
    public function test_can_map_Canvas_to_PPM_string(): void
    {
        $canvas = Canvas::create(10, 20, Color::create(0.0, 0.0, 0.0));

        $mapper = new PortablePixmapMapper;

        $this->assertStringEqualsFile(__DIR__ . '/fixture/10_20_empty.ppm', $mapper->map($canvas));
    }
}
