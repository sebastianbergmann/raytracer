<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function array_fill;
use function iterator_to_array;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\StateMachineInvariant;
use PHPUnit\Framework\Attributes\StateMachineRule;

/**
 * State machine for a canvas of 4 x 3 pixels.
 *
 * The model is the list of the colors of the pixels in the order in which the
 * canvas is iterated: row by row, from left to right.
 */
final class CanvasMachine
{
    private const int WIDTH  = 4;
    private const int HEIGHT = 3;
    private readonly Canvas $canvas;

    /**
     * @var array<int, Color>
     */
    private array $pixels;

    public function __construct(Color $background)
    {
        $this->canvas = Canvas::from(self::WIDTH, self::HEIGHT, $background);
        $this->pixels = array_fill(0, self::WIDTH * self::HEIGHT, $background);
    }

    /**
     * @param int<1, 4> $x
     * @param int<1, 3> $y
     */
    #[StateMachineRule]
    public function writePixel(int $x, int $y, Color $color): void
    {
        $this->canvas->writePixel($x, $y, $color);

        $this->pixels[($y - 1) * self::WIDTH + ($x - 1)] = $color;
    }

    #[StateMachineInvariant]
    public function canvasKeepsItsSize(): void
    {
        Assert::assertSame(self::WIDTH, $this->canvas->width());
        Assert::assertSame(self::HEIGHT, $this->canvas->height());
    }

    #[StateMachineInvariant]
    public function eachPixelHasTheColorThatWasWrittenToItLast(): void
    {
        for ($y = 1; $y <= self::HEIGHT; $y++) {
            for ($x = 1; $x <= self::WIDTH; $x++) {
                Assert::assertSame(
                    $this->pixels[($y - 1) * self::WIDTH + ($x - 1)],
                    $this->canvas->pixelAt($x, $y),
                );
            }
        }
    }

    #[StateMachineInvariant]
    public function canvasIsIteratedRowByRow(): void
    {
        Assert::assertSame($this->pixels, iterator_to_array($this->canvas, false));
    }
}
