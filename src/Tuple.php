<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Tuple
{
    /**
     * @var float
     */
    private $x;

    /**
     * @var float
     */
    private $y;

    /**
     * @var float
     */
    private $z;

    /**
     * @var float
     */
    private $w;

    public static function point(float $x, float $y, float $z): self
    {
        return new self($x, $y, $z, 1.0);
    }

    public static function vector(float $x, float $y, float $z): self
    {
        return new self($x, $y, $z, 0.0);
    }

    private function __construct(float $x, float $y, float $z, float $w)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->w = $w;
    }

    public function x(): float
    {
        return $this->x;
    }

    public function y(): float
    {
        return $this->y;
    }

    public function z(): float
    {
        return $this->z;
    }

    public function w(): float
    {
        return $this->w;
    }

    public function isPoint(): bool
    {
        return $this->w === 1.0;
    }

    public function isVector(): bool
    {
        return $this->w === 0.0;
    }

    public function plus(self $that): self
    {
        return new self(
            $this->x + $that->x(),
            $this->y + $that->y(),
            $this->z + $that->z(),
            $this->w + $that->w()
        );
    }
}
