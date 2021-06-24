<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Point extends Tuple
{
    public static function from(float $x, float $y, float $z): self
    {
        return new self($x, $y, $z, 1.0);
    }
}
