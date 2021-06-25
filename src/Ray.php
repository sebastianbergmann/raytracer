<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Ray
{
    private Tuple $origin;

    private Tuple $direction;

    public static function from(Tuple $origin, Tuple $direction): self
    {
        return new self($origin, $direction);
    }

    private function __construct(Tuple $origin, Tuple $direction)
    {
        $this->origin    = $origin;
        $this->direction = $direction;
    }

    public function origin(): Tuple
    {
        return $this->origin;
    }

    public function direction(): Tuple
    {
        return $this->direction;
    }

    /**
     * @throws RuntimeException
     */
    public function position(float $t): Tuple
    {
        return $this->origin->plus($this->direction->multiplyBy($t));
    }
}
