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

    public static function create(float $x, float $y, float $z, float $w): self
    {
        return new self($x, $y, $z, $w);
    }

    public static function createPoint(float $x, float $y, float $z): self
    {
        return new self($x, $y, $z, 1.0);
    }

    public static function createVector(float $x, float $y, float $z): self
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
        if ($this->isPoint() && $that->isPoint()) {
            throw new RuntimeException(
                'Cannot add point tuple to another point tuple'
            );
        }

        return new self(
            $this->x + $that->x(),
            $this->y + $that->y(),
            $this->z + $that->z(),
            $this->w + $that->w()
        );
    }

    public function minus(self $that): self
    {
        return new self(
            $this->x - $that->x(),
            $this->y - $that->y(),
            $this->z - $that->z(),
            $this->w - $that->w()
        );
    }

    public function negate(): self
    {
        return new self(
            -1 * $this->x,
            -1 * $this->y,
            -1 * $this->z,
            -1 * $this->w
        );
    }

    public function multiplyBy(float $factor): self
    {
        return new self(
            $factor * $this->x,
            $factor * $this->y,
            $factor * $this->z,
            $factor * $this->w
        );
    }

    public function divideBy(float $divisor): self
    {
        return new self(
            $this->x / $divisor,
            $this->y / $divisor,
            $this->z / $divisor,
            $this->w / $divisor
        );
    }

    public function magnitude(): float
    {
        return sqrt($this->x ** 2 + $this->y ** 2 + $this->z ** 2 + $this->w ** 2);
    }
}
