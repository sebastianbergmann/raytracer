<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function range;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<int, Color>
 */
final class Canvas implements IteratorAggregate
{
    /**
     * @var positive-int
     */
    private readonly int $width;

    /**
     * @var positive-int
     */
    private readonly int $height;

    /**
     * @var array<int,array<int,Color>>
     */
    private array $pixels;

    /**
     * @param positive-int $width
     * @param positive-int $height
     */
    public static function from(int $width, int $height, Color $background): self
    {
        return new self($width, $height, $background);
    }

    /**
     * @param positive-int $width
     * @param positive-int $height
     */
    private function __construct(int $width, int $height, Color $background)
    {
        $this->width  = $width;
        $this->height = $height;

        $this->initializePixels($background);
    }

    /**
     * @return positive-int
     */
    public function width(): int
    {
        return $this->width;
    }

    /**
     * @return positive-int
     */
    public function height(): int
    {
        return $this->height;
    }

    /**
     * @param positive-int $x
     * @param positive-int $y
     */
    public function pixelAt(int $x, int $y): Color
    {
        return $this->pixels[$x][$y];
    }

    /**
     * @param positive-int $x
     * @param positive-int $y
     */
    public function writePixel(int $x, int $y, Color $c): void
    {
        $this->pixels[$x][$y] = $c;
    }

    public function getIterator(): CanvasIterator
    {
        return new CanvasIterator($this);
    }

    private function initializePixels(Color $background): void
    {
        $this->pixels = [];

        foreach (range(1, $this->width) as $x) {
            foreach (range(1, $this->height) as $y) {
                $this->pixels[$x][$y] = $background;
            }
        }
    }
}
