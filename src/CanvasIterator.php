<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class CanvasIterator implements \Iterator
{
    /**
     * @var Canvas
     */
    private $canvas;

    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    /**
     * @var int
     */
    private $p;

    public function __construct(Canvas $canvas)
    {
        $this->canvas = $canvas;
    }

    public function rewind(): void
    {
        $this->x = 1;
        $this->y = 1;
        $this->p = 1;
    }

    public function valid(): bool
    {
        return $this->x <= $this->canvas->width() && $this->y <= $this->canvas->height();
    }

    public function key(): int
    {
        return $this->p;
    }

    public function current(): Color
    {
        return $this->canvas->pixelAt($this->x, $this->y);
    }

    public function next(): void
    {
        if ($this->x < $this->canvas->width()) {
            $this->x++;
        } else {
            $this->x = 1;
            $this->y++;
        }

        $this->p++;
    }
}
