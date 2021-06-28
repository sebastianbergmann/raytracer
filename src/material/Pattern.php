<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function floor;

final class Pattern
{
    private Color $a;

    private Color $b;

    private Matrix $transform;

    public static function from(Color $a, Color $b): self
    {
        return new self($a, $b);
    }

    private function __construct(Color $a, Color $b)
    {
        $this->a         = $a;
        $this->b         = $b;
        $this->transform = Matrix::identity(4);
    }

    public function a(): Color
    {
        return $this->a;
    }

    public function b(): Color
    {
        return $this->b;
    }

    public function stripeAt(Tuple $point): Color
    {
        if (floor($point->x()) % 2 === 0) {
            return $this->a;
        }

        return $this->b;
    }

    /**
     * @throws RuntimeException
     */
    public function stripeAtObject(Shape $object, Tuple $worldPoint): Color
    {
        $objectPoint  = $object->transform()->inverse()->multiplyBy($worldPoint);
        $patternPoint = $this->transform->inverse()->multiplyBy($objectPoint);

        return $this->stripeAt($patternPoint);
    }

    public function setTransform(Matrix $transform): void
    {
        $this->transform = $transform;
    }
}
