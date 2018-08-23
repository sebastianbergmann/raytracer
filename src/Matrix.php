<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Matrix
{
    /**
     * @var array
     */
    private $elements;

    public static function fromArray(array $elements): self
    {
        return new self($elements);
    }

    public function __construct(array $elements)
    {
        $this->ensureSize($elements);
        $this->ensureOnlyFloats($elements);

        $this->elements = $elements;
    }

    public function element(int $i, int $j): float
    {
        return $this->elements[$i][$j];
    }

    public function size(): int
    {
        return \count($this->elements);
    }

    private function ensureSize(array $elements): void
    {
        $numberOfRows = \count($elements);

        foreach ($elements as $row) {
            if (\count($row) !== $numberOfRows) {
                throw new InvalidArgumentException(
                    'Elements do not describe a MxM matrix'
                );
            }
        }
    }

    private function ensureOnlyFloats(array $elements): void
    {
        foreach ($elements as $row) {
            foreach ($row as $element) {
                if (!\is_float($element)) {
                    throw new InvalidArgumentException(
                        'Elements are not only float values'
                    );
                }
            }
        }
    }
}
