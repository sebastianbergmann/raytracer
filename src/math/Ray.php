<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final readonly class Ray
{
    private Tuple $origin;
    private Tuple $direction;

    /**
     * @throws InvalidArgumentException
     */
    public static function from(Tuple $origin, Tuple $direction): self
    {
        if ($direction->magnitude() === 0.0) {
            throw new InvalidArgumentException(
                'The direction of a ray must have a magnitude greater than zero',
            );
        }

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

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function transform(Matrix $m): self
    {
        return self::from(
            $m->multiplyBy($this->origin),
            $m->multiplyBy($this->direction),
        );
    }
}
