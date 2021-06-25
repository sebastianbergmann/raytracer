<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class PointLight
{
    private Tuple $position;

    private Color $intensity;

    public static function from(Tuple $position, Color $intensity): self
    {
        return new self($position, $intensity);
    }

    private function __construct(Tuple $position, Color $intensity)
    {
        $this->position  = $position;
        $this->intensity = $intensity;
    }

    public function position(): Tuple
    {
        return $this->position;
    }

    public function intensity(): Color
    {
        return $this->intensity;
    }
}
