<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function floor;

final class CheckersPattern extends Pattern
{
    private readonly Color $a;
    private readonly Color $b;

    protected function __construct(Color $a, Color $b)
    {
        $this->a = $a;
        $this->b = $b;

        parent::__construct();
    }

    public function localPatternAt(Shape $object, Tuple $point): Color
    {
        if ((floor($point->x) + floor($point->y) + floor($point->z)) % 2 === 0) {
            return $this->a;
        }

        return $this->b;
    }
}
