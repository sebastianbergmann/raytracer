<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function abs;
use function floor;

/**
 * @psalm-immutable
 */
final class Color
{
    private float $red;

    private float $green;

    private float $blue;

    public static function from(float $red, float $green, float $blue): self
    {
        return new self($red, $green, $blue);
    }

    private function __construct(float $red, float $green, float $blue)
    {
        $this->red   = $red;
        $this->green = $green;
        $this->blue  = $blue;
    }

    public function red(): float
    {
        return $this->red;
    }

    public function redAsInt(): int
    {
        return $this->floatToInt($this->red);
    }

    public function green(): float
    {
        return $this->green;
    }

    public function greenAsInt(): int
    {
        return $this->floatToInt($this->green);
    }

    public function blue(): float
    {
        return $this->blue;
    }

    public function blueAsInt(): int
    {
        return $this->floatToInt($this->blue);
    }

    public function plus(self $that): self
    {
        return new self(
            $this->red + $that->red(),
            $this->green + $that->green(),
            $this->blue + $that->blue()
        );
    }

    public function minus(self $that): self
    {
        return new self(
            $this->red - $that->red(),
            $this->green - $that->green(),
            $this->blue - $that->blue()
        );
    }

    public function multiplyBy(float $factor): self
    {
        return new self(
            $factor * $this->red,
            $factor * $this->green,
            $factor * $this->blue
        );
    }

    public function product(self $that): self
    {
        return new self(
            $this->red * $that->red(),
            $this->green * $that->green(),
            $this->blue * $that->blue()
        );
    }

    public function equalTo(self $that, float $delta = 0.00001): bool
    {
        if (abs($this->red - $that->red()) > $delta) {
            return false;
        }

        if (abs($this->green - $that->green()) > $delta) {
            return false;
        }

        if (abs($this->blue - $that->blue()) > $delta) {
            return false;
        }

        return true;
    }

    private function floatToInt(float $float): int
    {
        $int = (int) floor($float * 255);

        if ($int < 0) {
            return 0;
        }

        if ($int > 255) {
            return 255;
        }

        return $int;
    }
}
