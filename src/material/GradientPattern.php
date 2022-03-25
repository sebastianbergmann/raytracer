<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function floor;

final class GradientPattern extends Pattern
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
        $distance = $this->b->minus($this->a);
        $fraction = $point->x() - floor($point->x());

        return $this->a->plus($distance->multiplyBy($fraction));
    }
}
