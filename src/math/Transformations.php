<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function cos;
use function sin;

final class Transformations
{
    public static function translation(float $x, float $y, float $z): Matrix
    {
        return Matrix::fromArray(
            [
                [1.0, 0.0, 0.0, $x],
                [0.0, 1.0, 0.0, $y],
                [0.0, 0.0, 1.0, $z],
                [0.0, 0.0, 0.0, 1.0],
            ]
        );
    }

    public static function scaling(float $x, float $y, float $z): Matrix
    {
        return Matrix::fromArray(
            [
                [$x, 0.0, 0.0, 0.0],
                [0.0,  $y, 0.0, 0.0],
                [0.0, 0.0,  $z, 0.0],
                [0.0, 0.0, 0.0, 1.0],
            ]
        );
    }

    public static function rotationAroundX(float $r): Matrix
    {
        return Matrix::fromArray(
            [
                [1.0, 0.0, 0.0, 0.0],
                [0.0, cos($r), -sin($r), 0.0],
                [0.0, sin($r), cos($r), 0.0],
                [0.0, 0.0, 0.0, 1.0],
            ]
        );
    }

    public static function rotationAroundY(float $r): Matrix
    {
        return Matrix::fromArray(
            [
                [cos($r), 0.0, sin($r), 0.0],
                [0.0, 1.0, 0.0, 0.0],
                [-sin($r), 0.0, cos($r), 0.0],
                [0.0, 0.0, 0.0, 1.0],
            ]
        );
    }

    public static function rotationAroundZ(float $r): Matrix
    {
        return Matrix::fromArray(
            [
                [cos($r), -sin($r), 0.0, 0.0],
                [sin($r), cos($r), 0.0, 0.0],
                [0.0, 0.0, 1.0, 0.0],
                [0.0, 0.0, 0.0, 1.0],
            ]
        );
    }

    public static function shearing(float $xy, float $xz, float $yx, float $yz, float $zx, float $zy): Matrix
    {
        return Matrix::fromArray(
            [
                [1.0, $xy, $xz, 0.0],
                [$yx, 1.0, $yz, 0.0],
                [$zx, $zy, 1.0, 0.0],
                [0.0, 0.0, 0.0, 1.0],
            ]
        );
    }

    public static function view(Tuple $from, Tuple $to, Tuple $up): Matrix
    {
        return Matrix::identity(4);
    }
}
