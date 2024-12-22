<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final readonly class Intersection
{
    private float $t;
    private Shape $shape;

    public static function from(float $t, Shape $shape): self
    {
        return new self($t, $shape);
    }

    private function __construct(float $t, Shape $shape)
    {
        $this->t     = $t;
        $this->shape = $shape;
    }

    public function t(): float
    {
        return $this->t;
    }

    public function shape(): Shape
    {
        return $this->shape;
    }

    /**
     * @throws RuntimeException
     */
    public function prepare(Ray $r): PreparedComputation
    {
        $point  = $r->position($this->t);
        $eye    = $r->direction()->negate();
        $normal = $this->shape->normalAt($point);
        $inside = false;

        if ($eye->dot($normal) < 0) {
            $inside = true;
            $normal = $normal->negate();
        }

        $overPoint = $point->plus($normal->multiplyBy(0.00001));

        return new PreparedComputation(
            $this->t,
            $this->shape,
            $point,
            $overPoint,
            $eye,
            $normal,
            $inside,
        );
    }
}
