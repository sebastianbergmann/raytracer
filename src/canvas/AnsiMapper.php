<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const PHP_EOL;
use function file_put_contents;
use function sprintf;

/**
 * @codeCoverageIgnore
 */
final class AnsiMapper
{
    public function map(Canvas $canvas, string $target): void
    {
        $buffer = '';

        foreach (range(1, $canvas->height()) as $y) {
            foreach (range(1, $canvas->width()) as $x) {
                $color = $canvas->pixelAt($x, $y);

                $buffer .= sprintf(
                    "\x1b[48;2;%d;%d;%dm  ",
                    $color->redAsInt(),
                    $color->greenAsInt(),
                    $color->blueAsInt(),
                );
            }

            $buffer .= PHP_EOL;
        }

        file_put_contents(
            $target,
            $buffer
        );
    }
}
