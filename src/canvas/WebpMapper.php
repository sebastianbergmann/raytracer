<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function imagecolorallocate;
use function imagecreatetruecolor;
use function imagesetpixel;
use function imagewebp;

/**
 * @codeCoverageIgnore
 */
final readonly class WebpMapper
{
    /**
     * @throws RuntimeException
     */
    public function map(Canvas $canvas, string $target): void
    {
        $colors = [];

        $image = imagecreatetruecolor($canvas->width(), $canvas->height());

        if ($image === false) {
            throw new RuntimeException('Cannot create image');
        }

        for ($x = 1; $x <= $canvas->width(); $x++) {
            for ($y = 1; $y <= $canvas->height(); $y++) {
                $color = $canvas->pixelAt($x, $y);
                $key   = $color->red() . $color->green() . $color->blue();

                if (!isset($colors[$key])) {
                    $allocatedColor = imagecolorallocate(
                        $image,
                        $color->redAsInt(),
                        $color->greenAsInt(),
                        $color->blueAsInt(),
                    );

                    if ($allocatedColor === false) {
                        throw new RuntimeException('Cannot create image');
                    }

                    $colors[$key] = $allocatedColor;
                }

                imagesetpixel(
                    $image,
                    $x,
                    $y,
                    $colors[$key],
                );
            }
        }

        imagewebp($image, $target, 100);
    }
}
