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

    public function equalTo(self $that): bool
    {
        $size = $this->size();

        if ($this->size() !== $that->size()) {
            return false;
        }

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                if ($this->elements[$i][$j] !== $that->element($i, $j)) {
                    return false;
                }
            }
        }

        return true;
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
