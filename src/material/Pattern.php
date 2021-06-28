<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

abstract class Pattern
{
    private Matrix $transform;

    public static function checkers(Color $a, Color $b): CheckersPattern
    {
        return new CheckersPattern($a, $b);
    }

    public static function gradient(Color $a, Color $b): GradientPattern
    {
        return new GradientPattern($a, $b);
    }

    public static function ring(Color $a, Color $b): RingPattern
    {
        return new RingPattern($a, $b);
    }

    public static function stripe(Color $a, Color $b): StripePattern
    {
        return new StripePattern($a, $b);
    }

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
