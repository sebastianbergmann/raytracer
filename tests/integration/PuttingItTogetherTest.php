<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 *
 * @medium
 */
final class PuttingItTogetherTest extends TestCase
{
    public function test_chapter_4(): void
    {
        $canvas = (new PuttingItTogether)->chapter_4();

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_4.ansi',
            (new AnsiMapper)->map($canvas)
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_4.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }

    public function test_chapter_5(): void
    {
        $canvas = (new PuttingItTogether)->chapter_5();

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_5.ansi',
            (new AnsiMapper)->map($canvas)
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_5.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }

    public function test_chapter_6(): void
    {
        $canvas = (new PuttingItTogether)->chapter_6();

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_6.ansi',
            (new AnsiMapper)->map($canvas)
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_6.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }

    public function test_chapter_8(): void
    {
        $canvas = (new PuttingItTogether)->chapter_8();

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_8.ansi',
            (new AnsiMapper)->map($canvas)
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_8.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }

    public function test_chapter_10(): void
    {
        $canvas = (new PuttingItTogether)->chapter_10();

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_10.ansi',
            (new AnsiMapper)->map($canvas)
        );

        $this->assertStringEqualsFile(
            __DIR__ . '/../fixture/chapter_10.ppm',
            (new PortablePixmapMapper)->map($canvas)
        );
    }
}
