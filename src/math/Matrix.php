<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function abs;
use function array_keys;
use function array_values;
use function count;
use function range;

final class Matrix
{
    /**
     * @psalm-var array<int,array<int,float>>
     */
    private array $elements;

    /**
     * @psalm-param array<int,array<int,float>> $elements
     */
    public static function fromArray(array $elements): self
    {
        return new self($elements);
    }

    public static function identity(int $size): self
    {
        return new self(self::identityElements($size));
    }

    public static function translation(float $x, float $y, float $z): self
    {
        $elements = self::identityElements(4);

        $elements[0][3] = $x;
        $elements[1][3] = $y;
        $elements[2][3] = $z;

        return new self($elements);
    }

    /**
     * @psalm-param array<int,array<int,float>> $elements
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

    public function equalTo(self $that, float $delta = 0.0): bool
    {
        $size = $this->size();

        if ($this->size() !== $that->size()) {
            return false;
        }

        foreach (range(0, $size - 1) as $i) {
            foreach (range(0, $size - 1) as $j) {
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

        foreach (range(0, $size - 1) as $i) {
            foreach (range(0, $size - 1) as $j) {
                $result[$i][$j] = 0.0;
            }
        }

        foreach (range(0, $size - 1) as $i) {
            foreach (range(0, $size - 1) as $k) {
                foreach (range(0, $size - 1) as $j) {
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
                'Multiplication of matrix and tuple is only implemented for 4x4 matrices'
            );
        }

        $result = [0.0, 0.0, 0.0, 0.0];

        foreach (range(0, 3) as $i) {
            $result[$i] += $this->elements[$i][0] * $tuple->x();
            $result[$i] += $this->elements[$i][1] * $tuple->y();
            $result[$i] += $this->elements[$i][2] * $tuple->z();
            $result[$i] += $this->elements[$i][3] * $tuple->w();
        }

        return Tuple::create(...$result);
    }

    public function divideAllElementsBy(float $divisor): self
    {
        $elements = $this->elements;
        $size     = $this->size();

        foreach (range(0, $size - 1) as $i) {
            foreach (range(0, $size - 1) as $j) {
                $elements[$i][$j] /= $divisor;
            }
        }

        return new self($elements);
    }

    public function transpose(): self
    {
        $size   = $this->size();
        $result = [];

        foreach (range(0, $size - 1) as $i) {
            foreach (range(0, $size - 1) as $j) {
                $result[$j][$i] = $this->elements[$i][$j];
            }
        }

        return new self($result);
    }

    public function determinant(): float
    {
        $size = $this->size();

        if ($size === 2) {
            return $this->elements[0][0] * $this->elements[1][1] -
                   $this->elements[0][1] * $this->elements[1][0];
        }

        $determinant = 0.0;

        foreach (range(0, $size - 1) as $i) {
            $determinant += $this->cofactor(0, $i) * $this->elements[0][$i];
        }

        return $determinant;
    }

    public function submatrix(int $row, int $column): self
    {
        $elements = [];
        $tmp      = [];
        $size     = $this->size();

        foreach (range(0, $size - 1) as $i) {
            if ($i === $row) {
                continue;
            }

            foreach (range(0, $size - 1) as $j) {
                if ($j === $column) {
                    continue;
                }

                $tmp[$i][$j] = $this->elements[$i][$j];
            }
        }

        foreach (array_keys($tmp) as $key) {
            $elements[] = array_values($tmp[$key]);
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
        return $this->determinant() !== 0.0;
    }

    public function inverse(): self
    {
        return $this->cofactorMatrix()->transpose()->divideAllElementsBy($this->determinant());
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
                    'Elements do not describe a MxM matrix'
                );
            }
        }
    }

    private function cofactorMatrix(): self
    {
        $size   = $this->size();
        $result = [];

        foreach (range(0, $size - 1) as $i) {
            foreach (range(0, $size - 1) as $j) {
                $result[$i][$j] = $this->cofactor($i, $j);
            }
        }

        return new self($result);
    }

    /**
     * @psalm-return array<int,array<int,float>>
     */
    private static function identityElements(int $size): array
    {
        $elements = [];

        foreach (range(0, $size - 1) as $i) {
            foreach (range(0, $size - 1) as $j) {
                if ($i === $j) {
                    $elements[$i][$j] = 1.0;
                } else {
                    $elements[$i][$j] = 0.0;
                }
            }
        }

        return $elements;
    }
}
