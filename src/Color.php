<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Color
{
    /**
     * @var float
     */
    private $red;

    /**
     * @var float
     */
    private $green;

    /**
     * @var float
     */
    private $blue;

    private function __construct(float $red, float $green, float $blue)
    {
        $this->red   = $red;
        $this->green = $green;
        $this->blue  = $blue;
    }

    public static function create(float $red, float $green, float $blue): self
    {
        return new self($red, $green, $blue);
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
}
