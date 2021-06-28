<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class TestShape extends Shape
{
    private Ray $savedRay;

    public function savedRay(): Ray
    {
        return $this->savedRay;
    }

    protected function localNormalAt(Tuple $point): Tuple
    {
        return Tuple::vector($point->x(), $point->y(), $point->z());
    }

    protected function localIntersect(Ray $ray): IntersectionCollection
    {
        $this->savedRay = $ray;

        return IntersectionCollection::from();
    }
}
