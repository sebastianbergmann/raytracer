<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function abs;
use function sqrt;

/**
 * @psalm-suppress UnsafeInstantiation
 */
class Tuple
{
    private float $x;

    private float $y;

    private float $z;

    private float $w;

    public function __construct(float $x, float $y, float $z, float $w)
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

    /**
     * @throws RuntimeException
     */
    public function plus(self $that): static
    {
        if ($this->isPoint() && $that->isPoint()) {
            throw new RuntimeException(
                'Cannot add point tuple to another point tuple'
            );
        }

        return new static(
            $this->x + $that->x(),
            $this->y + $that->y(),
            $this->z + $that->z(),
            $this->w + $that->w()
        );
    }

    /**
     * @throws RuntimeException
     */
    public function minus(self $that): static
    {
        if ($this->isVector() && $that->isPoint()) {
            throw new RuntimeException(
                'Cannot subtract point tuple from a vector tuple'
            );
        }

        return new static(
            $this->x - $that->x(),
            $this->y - $that->y(),
            $this->z - $that->z(),
            $this->w - $that->w()
        );
    }

    public function negate(): static
    {
        return new static(
            -1 * $this->x,
            -1 * $this->y,
            -1 * $this->z,
            -1 * $this->w
        );
    }

    public function multiplyBy(float $factor): static
    {
        return new static(
            $factor * $this->x,
            $factor * $this->y,
            $factor * $this->z,
            $factor * $this->w
        );
    }

    public function divideBy(float $divisor): static
    {
        return new static(
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

    public function normalize(): static
    {
        $magnitude = $this->magnitude();

        return new static(
            $this->x / $magnitude,
            $this->y / $magnitude,
            $this->z / $magnitude,
            $this->w / $magnitude
        );
    }

    public function dot(self $that): float
    {
        return $this->x * $that->x() +
               $this->y * $that->y() +
               $this->z * $that->z() +
               $this->w * $that->w();
    }

    public function cross(self $that): Vector
    {
        return Vector::from(
            $this->y * $that->z() - $this->z * $that->y(),
            $this->z * $that->x() - $this->x * $that->z(),
            $this->x * $that->y() - $this->y * $that->x()
        );
    }

    public function equalTo(self $that, float $delta = 0.00000000000001): bool
    {
        if (abs($this->x - $that->x()) > $delta) {
            return false;
        }

        if (abs($this->y - $that->y()) > $delta) {
            return false;
        }

        if (abs($this->z - $that->z()) > $delta) {
            return false;
        }

        return true;
    }
}
