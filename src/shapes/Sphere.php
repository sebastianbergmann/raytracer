<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function sqrt;

final class Sphere extends Shape
{
    public function origin(): Tuple
    {
        return Tuple::point(0, 0, 0);
    }

    public function radius(): float
    {
        return 1.0;
    }

    /**
     * @throws RuntimeException
     */
    public function localIntersect(Ray $ray): IntersectionCollection
    {
        $sphereToRay = $ray->origin()->minus($this->origin());

        $a = $ray->direction()->dot($ray->direction());
        $b = 2 * $ray->direction()->dot($sphereToRay);
        $c = $sphereToRay->dot($sphereToRay) - 1;

        $discriminant = $b ** 2 - 4 * $a * $c;

        if ($discriminant < 0) {
            return IntersectionCollection::from();
        }

        $t1 = (-$b - sqrt($discriminant)) / (2 * $a);
        $t2 = (-$b + sqrt($discriminant)) / (2 * $a);

        return IntersectionCollection::from(
            Intersection::from($t1, $this),
            Intersection::from($t2, $this),
        );
    }

    public function localNormalAt(Tuple $point): Tuple
    {
        return Tuple::vector($point->x, $point->y, $point->z);
    }
}
