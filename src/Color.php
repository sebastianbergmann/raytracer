<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

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

    public function green(): float
    {
        return $this->green;
    }

    public function blue(): float
    {
        return $this->blue;
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

    public function equalTo(self $that): bool
    {
        /* @noinspection TypeUnsafeComparisonInspection */
        return $this->red == $that->red() &&
               $this->green == $that->green() &&
               $this->blue == $that->blue();
    }
}
