<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use RuntimeException;

final class IntersectionHasNoHitException extends RuntimeException implements Exception
{
}
