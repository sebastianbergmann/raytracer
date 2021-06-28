<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

/**
 * @psalm-immutable
 */
final class Intersection
{
    public const EPSILON = 0.00001;

    private float $t;

    private Object_ $object;

    public static function from(float $t, Object_ $object): self
    {
        return new self($t, $object);
    }

    private function __construct(float $t, Object_ $object)
    {
        $this->t      = $t;
        $this->object = $object;
    }

    public function t(): float
    {
        return $this->t;
    }

    public function object(): Object_
    {
        return $this->object;
    }

    /**
     * @throws RuntimeException
     */
    public function prepare(Ray $r): PreparedComputation
    {
        $point  = $r->position($this->t);
        $eye    = $r->direction()->negate();
        $normal = $this->object->normalAt($point);
        $inside = false;

        if ($eye->dot($normal) < 0) {
            $inside = true;
            $normal = $normal->negate();
        }

        $overPoint = $point->plus($normal->multiplyBy(self::EPSILON));

        return new PreparedComputation(
            $this->t,
            $this->object,
            $point,
            $overPoint,
            $eye,
            $normal,
            $inside
        );
    }
}
