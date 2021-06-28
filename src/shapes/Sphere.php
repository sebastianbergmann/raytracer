<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function sqrt;

final class Sphere extends Shape
{
    private Tuple $origin;

    private float $radius = 1.0;

    protected function __construct(Matrix $transform, Material $material)
    {
        $this->origin = Tuple::point(0, 0, 0);

        parent::__construct($transform, $material);
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
     * @psalm-mutation-free
     *
     * @throws RuntimeException
     */
    public function localIntersect(Ray $ray): IntersectionCollection
    {
        $sphereToRay = $ray->origin()->minus($this->origin);

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
            Intersection::from($t2, $this)
        );
    }

    /**
     * @psalm-mutation-free
     */
    protected function localNormalAt(Tuple $point): Tuple
    {
        return Tuple::vector($point->x(), $point->y(), $point->z());
    }
}
