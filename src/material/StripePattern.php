<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function floor;

final class StripePattern extends Pattern
{
    private Color $a;

    private Color $b;

    protected function __construct(Color $a, Color $b)
    {
        $this->a = $a;
        $this->b = $b;

        parent::__construct();
    }

    public function a(): Color
    {
        return $this->a;
    }

    public function b(): Color
    {
        return $this->b;
    }

    public function localPatternAt(Shape $object, Tuple $point): Color
    {
        if (floor($point->x()) % 2 === 0) {
            return $this->a;
        }

        return $this->b;
    }
}
