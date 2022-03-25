<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function abs;
use function sqrt;

/**
 * @psalm-immutable
 */
final class Tuple
{
    private float $x;
    private float $y;
    private float $z;
    private float $w;

    /**
     * @psalm-mutation-free
     */
    public static function from(float $x, float $y, float $z, float $w): self
    {
        return new self($x, $y, $z, $w);
    }

    /**
     * @psalm-mutation-free
     */
    public static function point(float $x, float $y, float $z): self
    {
        return new self($x, $y, $z, 1.0);
    }

    /**
     * @psalm-mutation-free
     */
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

    /**
     * @throws RuntimeException
     */
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

    /**
     * @throws RuntimeException
     */
    public function minus(self $that): self
    {
        if ($this->isVector() && $that->isPoint()) {
            throw new RuntimeException(
                'Cannot subtract point tuple from a vector tuple'
            );
        }

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

    public function normalize(): self
    {
        $magnitude = $this->magnitude();

        return new self(
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

    public function cross(self $that): self
    {
        return self::vector(
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

    /**
     * @throws RuntimeException
     */
    public function reflect(self $normal): self
    {
        return $this->minus($normal->multiplyBy(2 * $this->dot($normal)));
    }
}
