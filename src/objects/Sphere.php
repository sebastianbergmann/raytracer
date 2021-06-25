<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function sqrt;

final class Sphere implements Object_
{
    private Tuple $origin;

    private float $radius;

    public static function from(Tuple $origin, float $radius): self
    {
        return new self($origin, $radius);
    }

    public static function unit(): self
    {
        return new self(
            Tuple::point(0, 0, 0),
            1.0
        );
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
     * @throws RuntimeException
     */
    public function intersect(Ray $r): IntersectionCollection
    {
        $sphereToRay = $r->origin()->minus($this->origin);

        $a = $r->direction()->dot($r->direction());
        $b = 2 * $r->direction()->dot($sphereToRay);
        $c = $sphereToRay->dot($sphereToRay) - 1;

        $discrimiant = $b ** 2 - 4 * $a * $c;

        if ($discrimiant < 0) {
            return IntersectionCollection::from();
        }

        $t1 = (-$b - sqrt($discrimiant)) / (2 * $a);
        $t2 = (-$b + sqrt($discrimiant)) / (2 * $a);

        return IntersectionCollection::from(
            Intersection::from($t1, $this),
            Intersection::from($t2, $this)
        );
    }
}
