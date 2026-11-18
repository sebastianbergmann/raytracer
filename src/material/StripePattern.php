<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function floor;
use function fmod;

final class StripePattern extends Pattern
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
        if (fmod(floor($point->x), 2.0) === 0.0) {
            return $this->a;
        }

        return $this->b;
    }
}
