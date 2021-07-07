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

        foreach (range(1, $canvas->height(), 2) as $y) {
            foreach (range(1, $canvas->width()) as $x) {
                $bg = $canvas->pixelAt($x, $y);
                $fg = $canvas->pixelAt($x, $y + 1);

                $buffer .= sprintf(
                    "\x1b[48;2;%d;%d;%dm\x1b[38;2;%d;%d;%dm▄",
                    $bg->redAsInt(),
                    $bg->greenAsInt(),
                    $bg->blueAsInt(),
                    $fg->redAsInt(),
                    $fg->greenAsInt(),
                    $fg->blueAsInt(),
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
