<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function range;
use function tan;

final class Camera
{
    private int $horizontalSize;

    private int $verticalSize;

    private float $fieldOfView;

    private Matrix $transform;

    private float $halfView;

    private float $aspect;

    private float $cameraHalfWidth;

    private float $cameraHalfHeight;

    private float $pixelSize;

    public static function from(int $horizontalSize, int $verticalSize, float $fieldOfView): self
    {
        return new self($horizontalSize, $verticalSize, $fieldOfView, Matrix::identity(4));
    }

    private function __construct(int $horizontalSize, int $verticalSize, float $fieldOfView, Matrix $transform)
    {
        $this->horizontalSize = $horizontalSize;
        $this->verticalSize   = $verticalSize;
        $this->fieldOfView    = $fieldOfView;
        $this->transform      = $transform;

        $this->halfView = tan($this->fieldOfView / 2);
        $this->aspect   = $this->horizontalSize / $this->verticalSize;

        if ($this->aspect >= 1) {
            $this->cameraHalfWidth  = $this->halfView;
            $this->cameraHalfHeight = $this->halfView / $this->aspect;
        } else {
            $this->cameraHalfWidth  = $this->halfView * $this->aspect;
            $this->cameraHalfHeight = $this->halfView;
        }

        $this->pixelSize = ($this->cameraHalfWidth * 2) / $this->horizontalSize;
    }

    public function horizontalSize(): int
    {
        return $this->horizontalSize;
    }

    public function verticalSize(): int
    {
        return $this->verticalSize;
    }

    public function fieldOfView(): float
    {
        return $this->fieldOfView;
    }

    public function transform(): Matrix
    {
        return $this->transform;
    }

    public function halfView(): float
    {
        return $this->halfView;
    }

    public function aspect(): float
    {
        return $this->aspect;
    }

    public function cameraHalfWidth(): float
    {
        return $this->cameraHalfWidth;
    }

    public function cameraHalfHeight(): float
    {
        return $this->cameraHalfHeight;
    }

    public function pixelSize(): float
    {
        return $this->pixelSize;
    }

    public function setTransform(Matrix $transform): void
    {
        $this->transform = $transform;
    }

    /**
     * @throws RuntimeException
     */
    public function rayForPixel(int $px, int $py): Ray
    {
        $xOffset = ($px + 0.5) * $this->pixelSize;
        $yOffset = ($py + 0.5) * $this->pixelSize;

        $worldX = $this->cameraHalfWidth - $xOffset;
        $worldY = $this->cameraHalfHeight - $yOffset;

        $inverse   = $this->transform->inverse();
        $pixel     = $inverse->multiplyBy(Tuple::point($worldX, $worldY, -1));
        $origin    = $inverse->multiplyBy(Tuple::point(0, 0, 0));
        $direction = $pixel->minus($origin)->normalize();

        return Ray::from($origin, $direction);
    }

    public function render(World $world): Canvas
    {
        $canvas = Canvas::from($this->horizontalSize, $this->verticalSize, Color::from(0, 0, 0));

        foreach (range(1, $this->verticalSize) as $y) {
            foreach (range(1, $this->horizontalSize) as $x) {
                $ray   = $this->rayForPixel($x, $y);
                $color = $world->colorAt($ray);
                $canvas->writePixel($x, $y, $color);
            }
        }

        return $canvas;
    }
}
