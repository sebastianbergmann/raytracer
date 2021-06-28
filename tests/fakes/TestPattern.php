<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class TestPattern extends Pattern
{
    public static function default(): self
    {
        return new self;
    }

    public function localPatternAt(Shape $object, Tuple $point): Color
    {
        return Color::from($point->x(), $point->y(), $point->z());
    }
}
