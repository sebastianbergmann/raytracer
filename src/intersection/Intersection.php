<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Intersection
{
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
        $point = $r->position($this->t);

        return new PreparedComputation(
            $this->t,
            $this->object,
            $point,
            $r->direction()->negate(),
            $this->object->normalAt($point)
        );
    }
}
