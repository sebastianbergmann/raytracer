<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const PHP_EOL;
use function file_put_contents;
use function sprintf;

final class PortablePixmapMapper
{
    public function map(Canvas $canvas): string
    {
        $buffer = sprintf(
            "P3\n%d %d\n255\n",
            $canvas->width(),
            $canvas->height()
        );

        $pixels = 0;

        foreach ($canvas as $pixel) {
            $buffer .= sprintf(
                '%d %d %d',
                $pixel->red(),
                $pixel->green(),
                $pixel->blue()
            );

            $pixels++;

            if ($pixels === 5) {
                $buffer .= PHP_EOL;

                $pixels = 0;
            } else {
                $buffer .= ' ';
            }
        }

        return $buffer . PHP_EOL;
    }

    /**
     * @codeCoverageIgnore
     */
    public function mapToFile(Canvas $canvas, string $target): void
    {
        file_put_contents($target, $this->map($canvas));
    }
}
