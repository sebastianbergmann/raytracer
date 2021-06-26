<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use RuntimeException;

final class WorldHasNoLightException extends RuntimeException implements Exception
{
}
