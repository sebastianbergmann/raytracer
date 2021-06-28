<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

abstract class Pattern
{
    private Matrix $transform;

    protected function __construct()
    {
        $this->transform = Matrix::identity(4);
    }

    public function transform(): Matrix
    {
        return $this->transform;
    }

    public function setTransform(Matrix $transform): void
    {
        $this->transform = $transform;
    }

    /**
     * @throws RuntimeException
     */
    public function patternAt(Shape $object, Tuple $worldPoint): Color
    {
        $objectPoint  = $object->transform()->inverse()->multiplyBy($worldPoint);
        $patternPoint = $this->transform->inverse()->multiplyBy($objectPoint);

        return $this->localPatternAt($object, $patternPoint);
    }

    abstract public function localPatternAt(Shape $object, Tuple $point): Color;
}