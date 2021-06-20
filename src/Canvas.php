<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use IteratorAggregate;

final class Canvas implements IteratorAggregate
{
    private int $width;

    private int $height;

    private Color $background;

    /**
     * @psalm-var array<int,array<int,Color>>
     */
    private array $pixels;

    public static function create(int $width, int $height, Color $background): self
    {
        return new self($width, $height, $background);
    }

    private function __construct(int $width, int $height, Color $background)
    {
        $this->width      = $width;
        $this->height     = $height;
        $this->background = $background;

        $this->initializePixels();
    }

    public function width(): int
    {
        return $this->width;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function pixelAt(int $x, int $y): Color
    {
        return $this->pixels[$x][$y];
    }

    public function writePixel(int $x, int $y, Color $c): void
    {
        $this->pixels[$x][$y] = $c;
    }

    public function getIterator(): CanvasIterator
    {
        return new CanvasIterator($this);
    }

    private function initializePixels(): void
    {
        $this->pixels = [];

        for ($x = 1; $x <= $this->width; $x++) {
            for ($y = 1; $y <= $this->height; $y++) {
                $this->pixels[$x][$y] = $this->background;
            }
        }
    }
}
