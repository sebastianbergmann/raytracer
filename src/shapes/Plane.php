<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function abs;

final class Plane extends Shape
{
    /**
     * @psalm-mutation-free
     *
     * @throws RuntimeException
     */
    public function localIntersect(Ray $ray): IntersectionCollection
    {
        if (abs($ray->direction()->y()) < 0.00001) {
            return IntersectionCollection::from();
        }

        return IntersectionCollection::from(
            Intersection::from(
                -$ray->origin()->y() / $ray->direction()->y(),
                $this
            )
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function localNormalAt(Tuple $point): Tuple
    {
        return Tuple::vector(0, 1, 0);
    }
}
