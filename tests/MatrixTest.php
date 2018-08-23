<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Matrix
 *
 * @uses \SebastianBergmann\Raytracer\Tuple
 */
final class MatrixTest extends TestCase
{
    public function test_a_2x2_matrix_can_be_represented(): void
    {
        $matrix = Matrix::fromArray(
            [
                [-3.0, 5.0],
                [1.0, -2.0]
            ]
        );

        $this->assertSame(-3.0, $matrix->element(0, 0));
        $this->assertSame(5.0, $matrix->element(0, 1));
        $this->assertSame(1.0, $matrix->element(1, 0));
        $this->assertSame(-2.0, $matrix->element(1, 1));

        $this->assertSame(2, $matrix->size());
    }

    public function test_a_3x3_matrix_can_be_represented(): void
    {
        $matrix = Matrix::fromArray(
            [
                [-3.0, 5.0, 0.0],
                [1.0, -2.0, -7.0],
                [0.0, 1.0, 1.0]
            ]
        );

        $this->assertSame(-3.0, $matrix->element(0, 0));
        $this->assertSame(5.0, $matrix->element(0, 1));
        $this->assertSame(0.0, $matrix->element(0, 2));
        $this->assertSame(1.0, $matrix->element(1, 0));
        $this->assertSame(-2.0, $matrix->element(1, 1));
        $this->assertSame(-7.0, $matrix->element(1, 2));
        $this->assertSame(0.0, $matrix->element(2, 0));
        $this->assertSame(1.0, $matrix->element(2, 1));
        $this->assertSame(1.0, $matrix->element(2, 2));

        $this->assertSame(3, $matrix->size());
    }

    public function test_a_4x4_matrix_can_be_represented(): void
    {
        $matrix = Matrix::fromArray(
            [
                [1.0, 2.0, 3.0, 4.0],
                [5.5, 6.5, 7.5, 8.5],
                [9.0, 10.0, 11.0, 12.0],
                [13.5, 14.5, 15.5, 16.5]
            ]
        );

        $this->assertSame(1.0, $matrix->element(0, 0));
        $this->assertSame(2.0, $matrix->element(0, 1));
        $this->assertSame(3.0, $matrix->element(0, 2));
        $this->assertSame(4.0, $matrix->element(0, 3));
        $this->assertSame(5.5, $matrix->element(1, 0));
        $this->assertSame(6.5, $matrix->element(1, 1));
        $this->assertSame(7.5, $matrix->element(1, 2));
        $this->assertSame(8.5, $matrix->element(1, 3));
        $this->assertSame(9.0, $matrix->element(2, 0));
        $this->assertSame(10.0, $matrix->element(2, 1));
        $this->assertSame(11.0, $matrix->element(2, 2));
        $this->assertSame(12.0, $matrix->element(2, 3));
        $this->assertSame(13.5, $matrix->element(3, 0));
        $this->assertSame(14.5, $matrix->element(3, 1));
        $this->assertSame(15.5, $matrix->element(3, 2));
        $this->assertSame(16.5, $matrix->element(3, 3));

        $this->assertSame(4, $matrix->size());
    }

    public function test_an_MxN_matrix_cannot_be_represented(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Matrix::fromArray(
            [
                [1.0],
                [2.0, 3.0]
            ]
        );
    }

    public function test_can_only_represent_matrices_of_float_values(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Matrix::fromArray(
            [
                ['not a float']
            ]
        );
    }

    public function test_two_matrices_can_be_compared(): void
    {
        $a = Matrix::fromArray(
            [
                [-3.0, 5.0],
                [1.0, -2.0]
            ]
        );

        $b = Matrix::fromArray(
            [
                [3.0, -5.0],
                [-1.0, 2.0]
            ]
        );

        $c = Matrix::fromArray(
            [
                [-3.0, 5.0, 0.0],
                [1.0, -2.0, -7.0],
                [0.0, 1.0, 1.0]
            ]
        );

        $this->assertTrue($a->equalTo($a));
        $this->assertFalse($a->equalTo($b));
        $this->assertFalse($a->equalTo($c));
    }

    public function test_two_matrices_of_same_size_can_be_multiplied_together(): void
    {
        $a = Matrix::fromArray(
            [
                [1.0, 2.0, 3.0, 4.0],
                [2.0, 3.0, 4.0, 5.0],
                [3.0, 4.0, 5.0, 6.0],
                [4.0, 5.0, 6.0, 7.0]
            ]
        );

        $b = Matrix::fromArray(
            [
                [0.0, 1.0, 2.0, 4.0],
                [1.0, 2.0, 4.0, 8.0],
                [2.0, 4.0, 8.0, 16.0],
                [4.0, 8.0, 16.0, 32.0]
            ]
        );

        $c = $a->multiply($b);

        $this->assertTrue(
            $c->equalTo(
                Matrix::fromArray(
                    [
                        [24.0, 49.0, 98.0, 196.0],
                        [31.0, 64.0, 128.0, 256.0],
                        [38.0, 79.0, 158.0, 316.0],
                        [45.0, 94.0, 188.0, 376.0]
                    ]
                )
            )
        );
    }

    public function test_4x4_matrix_can_be_multiplied_by_a_tuple(): void
    {
        $a = Matrix::fromArray(
            [
                [1.0, 2.0, 3.0, 4.0],
                [2.0, 4.0, 4.0, 2.0],
                [8.0, 6.0, 4.0, 1.0],
                [0.0, 0.0, 0.0, 1.0]
            ]
        );

        $b = Tuple::createPoint(1.0, 2.0, 3.0);

        $this->assertTrue($a->multiplyBy($b)->equalTo(Tuple::createPoint(18.0, 24.0, 33.0)));
    }
}
