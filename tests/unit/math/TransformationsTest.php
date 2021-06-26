<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const M_PI;
use function sqrt;
use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Transformations
 *
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class TransformationsTest extends TestCase
{
    public function test_multiplying_a_point_by_a_translation_matrix_moves_the_point(): void
    {
        $transform = Transformations::translation(5, -3, 2);

        $p = Tuple::point(-3, 4, 5);

        $this->assertTrue($transform->multiplyBy($p)->equalTo(Tuple::point(2, 1, 7)));
    }

    public function test_multiplying_a_point_by_a_the_inverse_of_a_translation_matrix_moves_the_point_in_reverse(): void
    {
        $inverse = Transformations::translation(5, -3, 2)->inverse();

        $p = Tuple::point(-3, 4, 5);

        $this->assertTrue($inverse->multiplyBy($p)->equalTo(Tuple::point(-8, 7, 3)));
    }

    public function test_translation_does_not_affect_vectors(): void
    {
        $transform = Transformations::translation(5, -3, 2);

        $v = Tuple::vector(-3, 4, 5);

        $this->assertTrue($transform->multiplyBy($v)->equalTo($v));
    }

    public function test_applying_a_scaling_matrix_to_a_point_scales_the_point(): void
    {
        $transform = Transformations::scaling(2, 3, 4);

        $p = Tuple::point(-4, 6, 8);

        $this->assertTrue($transform->multiplyBy($p)->equalTo(Tuple::point(-8, 18, 32)));
    }

    public function test_applying_a_scaling_matrix_to_a_vector_scales_the_vector(): void
    {
        $transform = Transformations::scaling(2, 3, 4);

        $v = Tuple::vector(-4, 6, 8);

        $this->assertTrue($transform->multiplyBy($v)->equalTo(Tuple::vector(-8, 18, 32)));
    }

    public function test_applying_the_inverse_of_a_scaling_matrix_to_a_vector_scales_the_vector_in_reverse(): void
    {
        $inverse = Transformations::scaling(2, 3, 4)->inverse();

        $v = Tuple::vector(-4, 6, 8);

        $this->assertTrue($inverse->multiplyBy($v)->equalTo(Tuple::vector(-2, 2, 2)));
    }

    public function test_reflection_is_scaling_by_a_negative_value(): void
    {
        $transform = Transformations::scaling(-1, 1, 1);

        $p = Tuple::point(2, 3, 4);

        $this->assertTrue($transform->multiplyBy($p)->equalTo(Tuple::point(-2, 3, 4)));
    }

    public function test_a_point_can_be_rotated_around_the_X_axis(): void
    {
        $p = Tuple::point(0, 1, 0);

        $halfQuarter = Transformations::rotationAroundX(M_PI / 4);
        $fullQuarter = Transformations::rotationAroundX(M_PI / 2);

        $this->assertTrue($halfQuarter->multiplyBy($p)->equalTo(Tuple::point(0, sqrt(2) / 2, sqrt(2) / 2)));
        $this->assertTrue($fullQuarter->multiplyBy($p)->equalTo(Tuple::point(0, 0, 1)));
    }

    public function test_the_inverse_of_an_X_rotation_rotates_in_the_opposite_direction(): void
    {
        $p = Tuple::point(0, 1, 0);

        $inverse = Transformations::rotationAroundX(M_PI / 4)->inverse();

        $this->assertTrue($inverse->multiplyBy($p)->equalTo(Tuple::point(0, sqrt(2) / 2, -sqrt(2) / 2)));
    }

    public function test_a_point_can_be_rotated_around_the_Y_axis(): void
    {
        $p = Tuple::point(0, 0, 1);

        $halfQuarter = Transformations::rotationAroundY(M_PI / 4);
        $fullQuarter = Transformations::rotationAroundY(M_PI / 2);

        $this->assertTrue($halfQuarter->multiplyBy($p)->equalTo(Tuple::point(sqrt(2) / 2, 0, sqrt(2) / 2)));
        $this->assertTrue($fullQuarter->multiplyBy($p)->equalTo(Tuple::point(1, 0, 0)));
    }

    public function test_a_point_can_be_rotated_around_the_Z_axis(): void
    {
        $p = Tuple::point(0, 1, 0);

        $halfQuarter = Transformations::rotationAroundZ(M_PI / 4);
        $fullQuarter = Transformations::rotationAroundZ(M_PI / 2);

        $this->assertTrue($halfQuarter->multiplyBy($p)->equalTo(Tuple::point(-sqrt(2) / 2, sqrt(2) / 2, 0)));
        $this->assertTrue($fullQuarter->multiplyBy($p)->equalTo(Tuple::point(-1, 0, 0)));
    }

    public function test_a_shearing_transformation_moves_X_in_proportion_to_Y(): void
    {
        $p = Tuple::point(2, 3, 4);

        $transform = Transformations::shearing(1, 0, 0, 0, 0, 0);

        $this->assertTrue($transform->multiplyBy($p)->equalTo(Tuple::point(5, 3, 4)));
    }

    public function test_a_shearing_transformation_moves_X_in_proportion_to_Z(): void
    {
        $p = Tuple::point(2, 3, 4);

        $transform = Transformations::shearing(0, 1, 0, 0, 0, 0);

        $this->assertTrue($transform->multiplyBy($p)->equalTo(Tuple::point(6, 3, 4)));
    }

    public function test_a_shearing_transformation_moves_Y_in_proportion_to_X(): void
    {
        $p = Tuple::point(2, 3, 4);

        $transform = Transformations::shearing(0, 0, 1, 0, 0, 0);

        $this->assertTrue($transform->multiplyBy($p)->equalTo(Tuple::point(2, 5, 4)));
    }

    public function test_a_shearing_transformation_moves_Y_in_proportion_to_Z(): void
    {
        $p = Tuple::point(2, 3, 4);

        $transform = Transformations::shearing(0, 0, 0, 1, 0, 0);

        $this->assertTrue($transform->multiplyBy($p)->equalTo(Tuple::point(2, 7, 4)));
    }

    public function test_a_shearing_transformation_moves_Z_in_proportion_to_X(): void
    {
        $p = Tuple::point(2, 3, 4);

        $transform = Transformations::shearing(0, 0, 0, 0, 1, 0);

        $this->assertTrue($transform->multiplyBy($p)->equalTo(Tuple::point(2, 3, 6)));
    }

    public function test_a_shearing_transformation_moves_Z_in_proportion_to_Y(): void
    {
        $p = Tuple::point(2, 3, 4);

        $transform = Transformations::shearing(0, 0, 0, 0, 0, 1);

        $this->assertTrue($transform->multiplyBy($p)->equalTo(Tuple::point(2, 3, 7)));
    }

    public function test_individual_transformations_are_applied_in_sequence(): void
    {
        $p = Tuple::point(1, 0, 1);
        $A = Transformations::rotationAroundX(M_PI / 2);
        $B = Transformations::scaling(5, 5, 5);
        $C = Transformations::translation(10, 5, 7);

        $p2 = $A->multiplyBy($p);
        $this->assertTrue($p2->equalTo(Tuple::point(1, -1, 0)));

        $p3 = $B->multiplyBy($p2);
        $this->assertTrue($p3->equalTo(Tuple::point(5, -5, 0)));

        $p4 = $C->multiplyBy($p3);
        $this->assertTrue($p4->equalTo(Tuple::point(15, 0, 7)));
    }

    public function test_chained_transformations_must_be_applied_in_reverse_order(): void
    {
        $p = Tuple::point(1, 0, 1);
        $A = Transformations::rotationAroundX(M_PI / 2);
        $B = Transformations::scaling(5, 5, 5);
        $C = Transformations::translation(10, 5, 7);

        $T = $C->multiply($B)->multiply($A);

        $this->assertTrue($T->multiplyBy($p)->equalTo(Tuple::point(15, 0, 7)));
    }
}
