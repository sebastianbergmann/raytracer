<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

interface Object_
{
    public function material(): Material;

    public function intersect(Ray $r): IntersectionCollection;

    /**
     * @psalm-mutation-free
     */
    public function normalAt(Tuple $worldPoint): Tuple;
}
