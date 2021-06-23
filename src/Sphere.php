<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Sphere
{
    private Tuple $origin;

    private float $radius;

    public static function from(Tuple $origin, float $radius): self
    {
        return new self($origin, $radius);
    }

    private function __construct(Tuple $origin, float $radius)
    {
        $this->origin = $origin;
        $this->radius = $radius;
    }

    public function origin(): Tuple
    {
        return $this->origin;
    }

    public function radius(): float
    {
        return $this->radius;
    }
}
