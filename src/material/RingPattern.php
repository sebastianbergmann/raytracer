<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function floor;
use function sqrt;

final class RingPattern extends Pattern
{
    private Color $a;

    private Color $b;

    protected function __construct(Color $a, Color $b)
    {
        $this->a = $a;
        $this->b = $b;

        parent::__construct();
    }

    public function localPatternAt(Shape $object, Tuple $point): Color
    {
        if (floor(sqrt($point->x() ** 2 + $point->z() ** 2)) % 2 === 0) {
            return $this->a;
        }

        return $this->b;
    }
}
