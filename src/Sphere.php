<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function sqrt;

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

    /**
     * @psalm-return list<float>
     */
    public function intersect(Ray $r): array
    {
        $sphereToRay = $r->origin()->minus($this->origin);

        $a = $r->direction()->dot($r->direction());
        $b = 2 * $r->direction()->dot($sphereToRay);
        $c = $sphereToRay->dot($sphereToRay) - 1;

        $discrimiant = $b ** 2 - 4 * $a * $c;

        if ($discrimiant < 0) {
            return [];
        }

        $t1 = (-$b - sqrt($discrimiant)) / (2 * $a);
        $t2 = (-$b + sqrt($discrimiant)) / (2 * $a);

        return [$t1, $t2];
    }
}
