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

    public function test_the_4x4_identity_matrix_can_be_created(): void
    {
        $matrix = Matrix::identity(4);

        $this->assertSame(1.0, $matrix->element(0, 0));
        $this->assertSame(0.0, $matrix->element(0, 1));
        $this->assertSame(0.0, $matrix->element(0, 2));
        $this->assertSame(0.0, $matrix->element(0, 3));
        $this->assertSame(0.0, $matrix->element(1, 0));
        $this->assertSame(1.0, $matrix->element(1, 1));
        $this->assertSame(0.0, $matrix->element(1, 2));
        $this->assertSame(0.0, $matrix->element(1, 3));
        $this->assertSame(0.0, $matrix->element(2, 0));
        $this->assertSame(0.0, $matrix->element(2, 1));
        $this->assertSame(1.0, $matrix->element(2, 2));
        $this->assertSame(0.0, $matrix->element(2, 3));
        $this->assertSame(0.0, $matrix->element(3, 0));
        $this->assertSame(0.0, $matrix->element(3, 1));
        $this->assertSame(0.0, $matrix->element(3, 2));
        $this->assertSame(1.0, $matrix->element(3, 3));

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

    public function test_a_4x4_matrix_can_be_multiplied_by_the_4x4_identity_matrix(): void
    {
        $a = Matrix::fromArray(
            [
                [1.0, 2.0, 3.0, 4.0],
                [2.0, 3.0, 4.0, 5.0],
                [3.0, 4.0, 5.0, 6.0],
                [4.0, 5.0, 6.0, 7.0]
            ]
        );

        $i = Matrix::identity(4);

        $c = $a->multiply($i);

        $this->assertTrue($c->equalTo($a));
    }

    public function test_a_4x4_matrix_can_be_multiplied_by_a_tuple(): void
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

    public function test_other_matrices_cannot_be_multiplied_by_a_tuple(): void
    {
        $a = Matrix::fromArray(
            [
                [1.0, 2.0, 3.0],
                [2.0, 4.0, 4.0],
                [8.0, 6.0, 4.0]
            ]
        );

        $b = Tuple::createPoint(1.0, 2.0, 3.0);

        $this->expectException(RuntimeException::class);

        $a->multiplyBy($b);
    }

    public function test_a_matrix_can_be_transposed(): void
    {
        $a = Matrix::fromArray(
            [
                [0.0, 9.0, 3.0, 0.0],
                [9.0, 8.0, 0.0, 8.0],
                [1.0, 8.0, 5.0, 3.0],
                [0.0, 0.0, 5.0, 8.0]
            ]
        );

        $b = $a->transpose();

        $this->assertTrue(
            $b->equalTo(
                Matrix::fromArray(
                    [
                        [0.0, 9.0, 1.0, 0.0],
                        [9.0, 8.0, 8.0, 0.0],
                        [3.0, 0.0, 5.0, 5.0],
                        [0.0, 8.0, 3.0, 8.0]
                    ]
                )
            )
        );
    }

    public function test_the_identity_matrix_can_be_transposed(): void
    {
        $i = Matrix::identity(4);

        $this->assertTrue($i->equalTo($i->transpose()));
    }

    public function test_the_determinant_of_a_2x2_matrix_can_be_calculated(): void
    {
        $a = Matrix::fromArray(
            [
                [1.0, 5.0],
                [-3.0, 2.0]
            ]
        );

        $this->assertSame(17.0, $a->determinant());
    }

    public function test_a_submatrix_of_a_3x3_matrix_is_a_2x2_matrix(): void
    {
        $a = Matrix::fromArray(
            [
                [1.0, 5.0, 0.0],
                [-3.0, 2.0, 7.0],
                [0.0, 6.0, -3.0]
            ]
        );

        $this->assertTrue(
            $a->submatrix(0, 2)->equalTo(
                Matrix::fromArray(
                    [
                        [-3.0, 2.0],
                        [0.0, 6.0]
                    ]
                )
            )
        );
    }

    public function test_a_submatrix_of_a_4x4_matrix_is_a_3x3_matrix(): void
    {
        $a = Matrix::fromArray(
            [
                [-6.0, 1.0, 1.0, 6.0],
                [-8.0, 5.0, 8.0, 6.0],
                [-1.0, 0.0, 8.0, 2.0],
                [-7.0, 1.0, -1.0, 1.0]
            ]
        );

        $this->assertTrue(
            $a->submatrix(2, 1)->equalTo(
                Matrix::fromArray(
                    [
                        [-6.0, 1.0, 6.0],
                        [-8.0, 8.0, 6.0],
                        [-7.0, -1.0, 1.0]
                    ]
                )
            )
        );
    }

    public function test_the_minor_of_a_3x3_matrix_can_be_calculated(): void
    {
        $a = Matrix::fromArray(
            [
                [3.0, 5.0, 0.0],
                [2.0, -1.0, -7.0],
                [6.0, -1.0, 5.0]
            ]
        );

        $b = $a->submatrix(1, 0);

        $this->assertSame(25.0, $b->determinant());
        $this->assertSame(25.0, $a->minor(1, 0));
    }

    public function test_the_cofactor_of_a_3x3_matrix_can_be_calculated(): void
    {
        $a = Matrix::fromArray(
            [
                [3.0, 5.0, 0.0],
                [2.0, -1.0, -7.0],
                [6.0, -1.0, 5.0]
            ]
        );

        $this->assertSame(-12.0, $a->minor(0, 0));
        $this->assertSame(-12.0, $a->cofactor(0, 0));
        $this->assertSame(25.0, $a->minor(1, 0));
        $this->assertSame(-25.0, $a->cofactor(1, 0));
    }

    public function test_the_determinant_of_a_3x3_matrix_can_be_calculated(): void
    {
        $a = Matrix::fromArray(
            [
                [1.0, 2.0, 6.0],
                [-5.0, 8.0, -4.0],
                [2.0, 6.0, 4.0]
            ]
        );

        $this->assertSame(56.0, $a->cofactor(0, 0));
        $this->assertSame(12.0, $a->cofactor(0, 1));
        $this->assertSame(-46.0, $a->cofactor(0, 2));
        $this->assertSame(-196.0, $a->determinant());
    }

    public function test_the_determinant_of_a_4x4_matrix_can_be_calculated(): void
    {
        $a = Matrix::fromArray(
            [
                [-2.0, -8.0, 3.0, 5.0],
                [-3.0, 1.0, 7.0, 3.0],
                [1.0, 2.0, -9.0, 6.0],
                [-6.0, 7.0, 7.0, -9.0]
            ]
        );

        $this->assertSame(690.0, $a->cofactor(0, 0));
        $this->assertSame(447.0, $a->cofactor(0, 1));
        $this->assertSame(210.0, $a->cofactor(0, 2));
        $this->assertSame(51.0, $a->cofactor(0, 3));
        $this->assertSame(-4071.0, $a->determinant());
    }

    public function test_an_invertible_matrix_is_recognized_as_invertible(): void
    {
        $a = Matrix::fromArray(
            [
                [6.0, 4.0, 4.0, 4.0],
                [5.0, 5.0, 7.0, 6.0],
                [4.0, -9.0, 3.0, -7.0],
                [9.0, 1.0, 7.0, -6.0]
            ]
        );

        $this->assertSame(-2120.0, $a->determinant());
        $this->assertTrue($a->invertible());
    }

    /**
     * @testdox A non-invertible matrix is recognized as non-invertible
     */
    public function test_a_noninvertible_matrix_is_recognized_as_noninvertible(): void
    {
        $a = Matrix::fromArray(
            [
                [-4.0, 2.0, -2.0, -3.0],
                [9.0, 6.0, 2.0, 6.0],
                [0.0, -5.0, 1.0, -5.0],
                [0.0, 0.0, 0.0, 0.0]
            ]
        );

        $this->assertSame(0.0, $a->determinant());
        $this->assertFalse($a->invertible());
    }
}
