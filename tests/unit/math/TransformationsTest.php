<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const M_PI_2;
use const M_PI_4;
use function deg2rad;
use function sqrt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Transformations::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Tuple::class)]
#[Group('math')]
#[Small]
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

        $halfQuarter = Transformations::rotationAroundX(M_PI_4);
        $fullQuarter = Transformations::rotationAroundX(M_PI_2);

        $this->assertTrue($halfQuarter->multiplyBy($p)->equalTo(Tuple::point(0, sqrt(2) / 2, sqrt(2) / 2)));
        $this->assertTrue($fullQuarter->multiplyBy($p)->equalTo(Tuple::point(0, 0, 1)));
    }

    public function test_the_inverse_of_an_X_rotation_rotates_in_the_opposite_direction(): void
    {
        $p = Tuple::point(0, 1, 0);

        $inverse = Transformations::rotationAroundX(M_PI_4)->inverse();

        $this->assertTrue($inverse->multiplyBy($p)->equalTo(Tuple::point(0, sqrt(2) / 2, -sqrt(2) / 2)));
    }

    public function test_a_point_can_be_rotated_around_the_Y_axis(): void
    {
        $p = Tuple::point(0, 0, 1);

        $halfQuarter = Transformations::rotationAroundY(M_PI_4);
        $fullQuarter = Transformations::rotationAroundY(M_PI_2);

        $this->assertTrue($halfQuarter->multiplyBy($p)->equalTo(Tuple::point(sqrt(2) / 2, 0, sqrt(2) / 2)));
        $this->assertTrue($fullQuarter->multiplyBy($p)->equalTo(Tuple::point(1, 0, 0)));
    }

    public function test_a_point_can_be_rotated_around_the_Z_axis(): void
    {
        $p = Tuple::point(0, 1, 0);

        $halfQuarter = Transformations::rotationAroundZ(M_PI_4);
        $fullQuarter = Transformations::rotationAroundZ(M_PI_2);

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
        $A = Transformations::rotationAroundX(M_PI_2);
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
        $A = Transformations::rotationAroundX(M_PI_2);
        $B = Transformations::scaling(5, 5, 5);
        $C = Transformations::translation(10, 5, 7);

        $T = $C->multiply($B)->multiply($A);

        $this->assertTrue($T->multiplyBy($p)->equalTo(Tuple::point(15, 0, 7)));
    }

    public function test_the_view_transformation_matrix_for_the_default_orientation(): void
    {
        $from = Tuple::point(0, 0, 0);
        $to   = Tuple::point(0, 0, -1);
        $up   = Tuple::vector(0, 1, 0);

        $t = Transformations::view($from, $to, $up);

        $this->assertTrue($t->equalTo(Matrix::identity(4)));
    }

    public function test_a_view_transformation_matrix_looking_in_positive_Z_direction(): void
    {
        $from = Tuple::point(0, 0, 0);
        $to   = Tuple::point(0, 0, 1);
        $up   = Tuple::vector(0, 1, 0);

        $t = Transformations::view($from, $to, $up);

        $this->assertTrue($t->equalTo(Transformations::scaling(-1, 1, -1)));
    }

    public function test_the_view_transformation_moves_the_world(): void
    {
        $from = Tuple::point(0, 0, 8);
        $to   = Tuple::point(0, 0, 0);
        $up   = Tuple::vector(0, 1, 0);

        $t = Transformations::view($from, $to, $up);

        $this->assertTrue($t->equalTo(Transformations::translation(0, 0, -8)));
    }

    public function test_an_arbitrary_view_transformation(): void
    {
        $from = Tuple::point(1, 3, 2);
        $to   = Tuple::point(4, -2, 8);
        $up   = Tuple::vector(1, 1, 0);

        $t = Transformations::view($from, $to, $up);

        $this->assertTrue(
            $t->equalTo(
                Matrix::fromArray(
                    [
                        [-0.50709, 0.50709, 0.67612, -2.36643],
                        [0.76772, 0.60609, 0.12122, -2.82843],
                        [-0.35857, 0.59761, -0.71714, 0],
                        [0, 0, 0, 1],
                    ],
                ),
                0.00001,
            ),
        );
    }

    #[Property]
    public function test_a_translation_moves_points_by_its_offsets_and_does_not_affect_vectors(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $offsets,
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $p,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $v,
    ): void {
        $translation = Transformations::translation($offsets->x, $offsets->y, $offsets->z);

        $this->assertEquals($p->plus($offsets), $translation->multiplyBy($p));
        $this->assertEquals($v, $translation->multiplyBy($v));
    }

    #[Property]
    public function test_translations_are_combined_by_adding_their_offsets_and_undone_by_negating_them(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $a,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $b,
    ): void {
        $sum = $a->plus($b);

        $this->assertEquals(
            Transformations::translation($sum->x, $sum->y, $sum->z),
            Transformations::translation($a->x, $a->y, $a->z)->multiply(Transformations::translation($b->x, $b->y, $b->z)),
        );

        $this->assertEquals(
            Transformations::translation(-$a->x, -$a->y, -$a->z),
            Transformations::translation($a->x, $a->y, $a->z)->inverse(),
        );
    }

    #[Property]
    public function test_a_scaling_multiplies_each_coordinate_by_its_factor(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $factors,
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $p,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $v,
    ): void {
        $scaling = Transformations::scaling($factors->x, $factors->y, $factors->z);

        $this->assertEquals(
            Tuple::point($factors->x * $p->x, $factors->y * $p->y, $factors->z * $p->z),
            $scaling->multiplyBy($p),
        );

        $this->assertEquals(
            Tuple::vector($factors->x * $v->x, $factors->y * $v->y, $factors->z * $v->z),
            $scaling->multiplyBy($v),
        );
    }

    #[Property]
    public function test_scalings_are_combined_by_multiplying_their_factors_and_undone_by_inverting_them(
        #[ForAllExternal(RaytracerGenerators::class, 'scalingFactors')]
        float $x1,
        #[ForAllExternal(RaytracerGenerators::class, 'scalingFactors')]
        float $y1,
        #[ForAllExternal(RaytracerGenerators::class, 'scalingFactors')]
        float $z1,
        #[ForAllExternal(RaytracerGenerators::class, 'scalingFactors')]
        float $x2,
        #[ForAllExternal(RaytracerGenerators::class, 'scalingFactors')]
        float $y2,
        #[ForAllExternal(RaytracerGenerators::class, 'scalingFactors')]
        float $z2,
    ): void {
        $this->assertEquals(
            Transformations::scaling($x1 * $x2, $y1 * $y2, $z1 * $z2),
            Transformations::scaling($x1, $y1, $z1)->multiply(Transformations::scaling($x2, $y2, $z2)),
        );

        $this->assertEqualsWithDelta(
            Transformations::scaling(1 / $x1, 1 / $y1, 1 / $z1),
            Transformations::scaling($x1, $y1, $z1)->inverse(),
            0.000000000001,
        );
    }

    /**
     * @param 'x'|'y'|'z'    $axis
     * @param int<-360, 360> $a
     * @param int<-360, 360> $b
     */
    #[Property]
    public function test_rotations_around_the_same_axis_are_combined_by_adding_their_angles_and_undone_by_negating_them(string $axis, int $a, int $b): void
    {
        $this->assertEqualsWithDelta(
            $this->rotation($axis, $a + $b),
            $this->rotation($axis, $a)->multiply($this->rotation($axis, $b)),
            0.000000000001,
        );

        $this->assertEqualsWithDelta(
            $this->rotation($axis, -$a),
            $this->rotation($axis, $a)->inverse(),
            0.000000000001,
        );

        $this->assertEquals(
            $this->rotation($axis, -$a),
            $this->rotation($axis, $a)->transpose(),
        );
    }

    /**
     * @param 'x'|'y'|'z'    $axis
     * @param int<-360, 360> $degrees
     */
    #[Property]
    public function test_a_rotation_preserves_lengths_and_angles_and_does_not_move_its_axis(
        string $axis,
        int $degrees,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $u,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $v,
    ): void {
        $rotation = $this->rotation($axis, $degrees);

        $unitVectorOfAxis = match ($axis) {
            'x' => Tuple::vector(1, 0, 0),
            'y' => Tuple::vector(0, 1, 0),
            'z' => Tuple::vector(0, 0, 1),
        };

        $this->assertEqualsWithDelta(1.0, $rotation->determinant(), 0.000000000001);
        $this->assertEqualsWithDelta($u->magnitude(), $rotation->multiplyBy($u)->magnitude(), 0.000000001);
        $this->assertEqualsWithDelta($u->dot($v), $rotation->multiplyBy($u)->dot($rotation->multiplyBy($v)), 0.000001);
        $this->assertEquals($unitVectorOfAxis, $rotation->multiplyBy($unitVectorOfAxis));
    }

    #[Property]
    public function test_a_shearing_moves_each_coordinate_in_proportion_to_the_other_two_coordinates(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $ofX,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $ofY,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $ofZ,
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $p,
    ): void {
        $shearing = Transformations::shearing($ofX->y, $ofX->z, $ofY->x, $ofY->z, $ofZ->x, $ofZ->y);

        $this->assertEquals(
            Tuple::point(
                $p->x + $ofX->y * $p->y + $ofX->z * $p->z,
                $p->y + $ofY->x * $p->x + $ofY->z * $p->z,
                $p->z + $ofZ->x * $p->x + $ofZ->y * $p->y,
            ),
            $shearing->multiplyBy($p),
        );
    }

    #[Property]
    public function test_the_view_transformation_moves_the_eye_to_the_origin_and_what_it_looks_at_onto_the_negative_Z_axis(
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $from,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroVectors')]
        Tuple $direction,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroVectors')]
        Tuple $up,
    ): void {
        $to   = $from->plus($direction);
        $view = Transformations::view($from, $to, $up);

        $this->assertEqualsWithDelta(Tuple::point(0, 0, 0), $view->multiplyBy($from), 0.0000001);
        $this->assertEqualsWithDelta(Tuple::point(0, 0, -$direction->magnitude()), $view->multiplyBy($to), 0.0000001);
    }

    #[Property]
    public function test_there_is_no_view_transformation_for_an_eye_that_looks_at_its_own_position(
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $from,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroVectors')]
        Tuple $up,
    ): void {
        $this->expectException(RuntimeException::class);

        /* @noinspection UnusedFunctionResultInspection */
        Transformations::view($from, $from, $up);
    }

    /**
     * @param 'x'|'y'|'z' $axis
     */
    private function rotation(string $axis, int $degrees): Matrix
    {
        return match ($axis) {
            'x' => Transformations::rotationAroundX(deg2rad($degrees)),
            'y' => Transformations::rotationAroundY(deg2rad($degrees)),
            'z' => Transformations::rotationAroundZ(deg2rad($degrees)),
        };
    }
}
