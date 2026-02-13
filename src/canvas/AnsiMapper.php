<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const PHP_EOL;
use function file_put_contents;
use function sprintf;

final readonly class AnsiMapper
{
    public function map(Canvas $canvas): string
    {
        $buffer = "\x1b[2J\x1b[H";

        for ($y = 1; $y <= $canvas->height(); $y += 2) {
            for ($x = 1; $x <= $canvas->width(); $x++) {
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

        return $buffer . "\x1b[0";
    }

    /**
     * @codeCoverageIgnore
     */
    public function mapToFile(Canvas $canvas, string $target): void
    {
        file_put_contents($target, $this->map($canvas));
    }
}
