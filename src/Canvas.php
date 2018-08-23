<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Canvas
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var Color
     */
    private $background;

    /**
     * @var int
     */
    private $pixels;

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

    public function pixelAt(int $x, int $y): Color
    {
        return $this->pixels[$x][$y];
    }

    private function initializePixels(): void
    {
        for ($x = 1; $x <= $this->width; $x++) {
            for ($y = 1; $y <= $this->height; $y++) {
                $this->pixels[$x][$y] = $this->background;
            }
        }
    }
}
