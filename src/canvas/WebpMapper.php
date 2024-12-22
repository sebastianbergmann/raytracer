<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function imagecolorallocate;
use function imagecreatetruecolor;
use function imagesetpixel;
use function imagewebp;
use function range;

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

        foreach (range(1, $canvas->width()) as $x) {
            foreach (range(1, $canvas->height()) as $y) {
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
