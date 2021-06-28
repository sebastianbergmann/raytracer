<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

/**
 * @psalm-immutable
 */
final class PreparedComputation
{
    private float $t;

    private Object_ $object;

    private Tuple $point;

    private Tuple $overPoint;

    private Tuple $eye;

    private Tuple $normal;

    private bool $inside;

    public function __construct(float $t, Object_ $object, Tuple $point, Tuple $overPoint, Tuple $eye, Tuple $normal, bool $inside)
    {
        $this->t         = $t;
        $this->object    = $object;
        $this->point     = $point;
        $this->overPoint = $overPoint;
        $this->eye       = $eye;
        $this->normal    = $normal;
        $this->inside    = $inside;
    }

    public function t(): float
    {
        return $this->t;
    }

    public function object(): Object_
    {
        return $this->object;
    }

    public function point(): Tuple
    {
        return $this->point;
    }

    public function overPoint(): Tuple
    {
        return $this->overPoint;
    }

    public function eye(): Tuple
    {
        return $this->eye;
    }

    public function normal(): Tuple
    {
        return $this->normal;
    }

    public function inside(): bool
    {
        return $this->inside;
    }
}
