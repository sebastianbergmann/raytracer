<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Ray
{
    private Point $origin;

    private Vector $direction;

    public static function from(Point $origin, Vector $direction): self
    {
        return new self($origin, $direction);
    }

    private function __construct(Point $origin, Vector $direction)
    {
        $this->origin    = $origin;
        $this->direction = $direction;
    }

    public function origin(): Point
    {
        return $this->origin;
    }

    public function direction(): Vector
    {
        return $this->direction;
    }

    /**
     * @throws RuntimeException
     */
    public function position(float $t): Point
    {
        return $this->origin->plus($this->direction->multiplyBy($t));
    }
}
