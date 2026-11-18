<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function abs;
use function array_values;
use function count;
use function sqrt;

final readonly class Matrix
{
    /**
     * @var array<int,array<int,float>>
     */
    private array $elements;

    /**
     * @param array<int,array<int,float>> $elements
     */
    public static function fromArray(array $elements): self
    {
        return new self($elements);
    }

    public static function identity(int $size): self
    {
        $elements = [];

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                if ($i === $j) {
                    $elements[$i][$j] = 1.0;
                } else {
                    $elements[$i][$j] = 0.0;
                }
            }
        }

        return new self($elements);
    }

    /**
     * @param array<int,array<int,float>> $elements
     */
    public function __construct(array $elements)
    {
        $this->ensureSize($elements);

        $this->elements = $elements;
    }

    public function element(int $i, int $j): float
    {
        return $this->elements[$i][$j];
    }

    public function size(): int
    {
        return count($this->elements);
    }

    /**
     * @param non-negative-float $delta
     */
    public function equalTo(self $that, float $delta = 0.00000000000001): bool
    {
        $size = $this->size();

        if ($this->size() !== $that->size()) {
            return false;
        }

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                if (abs($this->elements[$i][$j] - $that->element($i, $j)) > $delta) {
                    return false;
                }
            }
        }

        return true;
    }

    public function multiply(self $that): self
    {
        $size   = $this->size();
        $result = [];

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $result[$i][$j] = 0.0;
            }
        }

        for ($i = 0; $i < $size; $i++) {
            for ($k = 0; $k < $size; $k++) {
                for ($j = 0; $j < $size; $j++) {
                    $result[$i][$k] += $this->elements[$i][$j] * $that->element($j, $k);
                }
            }
        }

        return new self($result);
    }

    /**
     * @throws RuntimeException
     */
    public function multiplyBy(Tuple $tuple): Tuple
    {
        if ($this->size() !== 4) {
            throw new RuntimeException(
                'Multiplication of matrix and tuple is only implemented for 4x4 matrices',
            );
        }

        $result = [];

        for ($i = 0; $i <= 3; $i++) {
            $result[$i] = $this->elements[$i][0] * $tuple->x +
                          $this->elements[$i][1] * $tuple->y +
                          $this->elements[$i][2] * $tuple->z +
                          $this->elements[$i][3] * $tuple->w;
        }

        return Tuple::from(
            $result[0],
            $result[1],
            $result[2],
            $result[3],
        );
    }

    public function divideAllElementsBy(float $divisor): self
    {
        $elements = $this->elements;
        $size     = $this->size();

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $elements[$i][$j] /= $divisor;
            }
        }

        return new self($elements);
    }

    public function transpose(): self
    {
        $size   = $this->size();
        $result = [];

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $result[$j][$i] = $this->elements[$i][$j];
            }
        }

        return new self($result);
    }

    public function determinant(): float
    {
        $size = $this->size();

        if ($size === 0) {
            return 1.0;
        }

        if ($size === 2) {
            return $this->elements[0][0] * $this->elements[1][1] -
                   $this->elements[0][1] * $this->elements[1][0];
        }

        $determinant = 0.0;

        for ($i = 0; $i < $size; $i++) {
            $determinant += $this->cofactor(0, $i) * $this->elements[0][$i];
        }

        return $determinant;
    }

    public function submatrix(int $row, int $column): self
    {
        $elements = [];

        foreach ($this->elements as $i => $currentRow) {
            if ($i === $row) {
                continue;
            }

            unset($currentRow[$column]);

            $elements[] = array_values($currentRow);
        }

        return new self($elements);
    }

    public function minor(int $row, int $column): float
    {
        return $this->submatrix($row, $column)->determinant();
    }

    public function cofactor(int $row, int $column): float
    {
        $minor = $this->minor($row, $column);

        if (($row + $column) % 2 !== 0) {
            $minor *= -1.0;
        }

        return $minor;
    }

    public function invertible(): bool
    {
        return !$this->isSingularFor($this->determinant());
    }

    /**
     * @throws RuntimeException
     */
    public function inverse(): self
    {
        $determinant = $this->determinant();

        if ($this->isSingularFor($determinant)) {
            throw new RuntimeException(
                'Matrix is not invertible',
            );
        }

        return $this->cofactorMatrix()->transpose()->divideAllElementsBy($determinant);
    }

    /**
     * @param array<int,array<int,float>> $elements
     */
    private function ensureSize(array $elements): void
    {
        $numberOfRows = count($elements);

        foreach ($elements as $row) {
            if (count($row) !== $numberOfRows) {
                throw new InvalidArgumentException(
                    'Elements do not describe a MxM matrix',
                );
            }
        }
    }

    /**
     * The determinant of a singular matrix is zero. When it is calculated
     * with floating-point numbers, it is usually not exactly zero, though.
     *
     * The absolute value of a determinant is at most the product of the
     * lengths of the rows of its matrix (Hadamard's inequality). A matrix is
     * therefore considered singular when its determinant is negligible in
     * comparison with this product.
     */
    private function isSingularFor(float $determinant): bool
    {
        $bound = 1.0;

        foreach ($this->elements as $row) {
            $sumOfSquares = 0.0;

            foreach ($row as $element) {
                $sumOfSquares += $element ** 2;
            }

            $bound *= sqrt($sumOfSquares);
        }

        return abs($determinant) <= 0.0000000001 * $bound;
    }

    private function cofactorMatrix(): self
    {
        $size   = $this->size();
        $result = [];

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $result[$i][$j] = $this->cofactor($i, $j);
            }
        }

        return new self($result);
    }
}
