<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use const NAN;
use function abs;
use function is_finite;
use function sqrt;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(Tuple::class)]
#[Group('math')]
#[Small]
final class TupleTest extends TestCase
{
    /**
     * @return non-empty-list<array{0: float, 1: float, 2: float, 3: float}>
     */
    public static function magnitudeProvider(): array
    {
        return [
            [1.0, 1.0, 0.0, 0.0],
            [1.0, 0.0, 1.0, 0.0],
            [1.0, 0.0, 0.0, 1.0],
            [sqrt(14), 1.0, 2.0, 3.0],
            [sqrt(14), -1.0, -2.0, -3.0],
        ];
    }

    /**
     * @return non-empty-list<array{0: float, 1: float, 2: float, 3: float}>
     */
    public static function normalizationProvider(): array
    {
        return [
            [4.0, 0.0, 0.0, 1.0, 0.0, 0.0],
            [1.0, 2.0, 3.0, 1 / sqrt(14), 2 / sqrt(14), 3 / sqrt(14)],
        ];
    }

    #[TestDox('A tuple with w=1.0 is a point')]
    public function test_a_tuple_with_w_equal_to_one_is_a_point(): void
    {
        $p = Tuple::point(4.3, -4.2, 3.1);

        $this->assertSame(4.3, $p->x);
        $this->assertSame(-4.2, $p->y);
        $this->assertSame(3.1, $p->z);
        $this->assertSame(1.0, $p->w);
        $this->assertTrue($p->isPoint());
        $this->assertFalse($p->isVector());
    }

    #[TestDox('A tuple with w=0.0 is a vector')]
    public function test_a_tuple_with_w_equal_to_zero_is_a_vector(): void
    {
        $v = Tuple::vector(4.3, -4.2, 3.1);

        $this->assertSame(4.3, $v->x);
        $this->assertSame(-4.2, $v->y);
        $this->assertSame(3.1, $v->z);
        $this->assertSame(0.0, $v->w);
        $this->assertFalse($v->isPoint());
        $this->assertTrue($v->isVector());
    }

    #[TestDox('A tuple with w!=1.0 and w!=0.0 is neither a point nor a vector')]
    public function test_can_be_generic(): void
    {
        $t = Tuple::from(1.0, -2.0, 3.0, -4.0);

        $this->assertSame(1.0, $t->x);
        $this->assertSame(-2.0, $t->y);
        $this->assertSame(3.0, $t->z);
        $this->assertSame(-4.0, $t->w);
        $this->assertFalse($t->isPoint());
        $this->assertFalse($t->isVector());
    }

    public function test_a_vector_can_be_added_to_a_point(): void
    {
        $p1 = Tuple::point(3.0, -2.0, 5.0);
        $v  = Tuple::vector(-2.0, 3.0, 1.0);

        $p2 = $p1->plus($v);

        $this->assertSame(1.0, $p2->x);
        $this->assertSame(1.0, $p2->y);
        $this->assertSame(6.0, $p2->z);
        $this->assertSame(1.0, $p2->w);
    }

    public function test_a_vector_can_be_added_to_another_vector(): void
    {
        $v1 = Tuple::vector(3.0, -2.0, 5.0);
        $v2 = Tuple::vector(-2.0, 3.0, 1.0);

        $v3 = $v1->plus($v2);

        $this->assertSame(1.0, $v3->x);
        $this->assertSame(1.0, $v3->y);
        $this->assertSame(6.0, $v3->z);
        $this->assertSame(0.0, $v3->w);
    }

    public function test_a_point_cannot_be_added_to_a_point(): void
    {
        $p = Tuple::point(3.0, -2.0, 5.0);

        $this->expectException(RuntimeException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $p->plus($p);
    }

    public function test_a_point_can_be_subtracted_from_another_point(): void
    {
        $p1 = Tuple::point(3.0, 2.0, 1.0);
        $p2 = Tuple::point(5.0, 6.0, 7.0);

        $v = $p1->minus($p2);

        $this->assertSame(-2.0, $v->x);
        $this->assertSame(-4.0, $v->y);
        $this->assertSame(-6.0, $v->z);
        $this->assertSame(0.0, $v->w);
    }

    public function test_a_vector_can_be_subtracted_from_a_point(): void
    {
        $p1 = Tuple::point(3.0, 2.0, 1.0);
        $v  = Tuple::vector(5.0, 6.0, 7.0);

        $p2 = $p1->minus($v);

        $this->assertSame(-2.0, $p2->x);
        $this->assertSame(-4.0, $p2->y);
        $this->assertSame(-6.0, $p2->z);
        $this->assertSame(1.0, $p2->w);
    }

    public function test_a_vector_can_be_subtracted_from_another_vector(): void
    {
        $v1 = Tuple::vector(3.0, 2.0, 1.0);
        $v2 = Tuple::vector(5.0, 6.0, 7.0);

        $v3 = $v1->minus($v2);

        $this->assertSame(-2.0, $v3->x);
        $this->assertSame(-4.0, $v3->y);
        $this->assertSame(-6.0, $v3->z);
        $this->assertSame(0.0, $v3->w);
    }

    public function test_a_point_cannot_be_subtracted_from_a_vector(): void
    {
        $v = Tuple::vector(0, 0, 0);
        $p = Tuple::point(0, 0, 0);

        $this->expectException(RuntimeException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $v->minus($p);
    }

    public function test_can_be_negated(): void
    {
        $t1 = Tuple::from(1.0, -2.0, 3.0, -4.0);

        $t2 = $t1->negate();

        $this->assertSame(-1.0, $t2->x);
        $this->assertSame(2.0, $t2->y);
        $this->assertSame(-3.0, $t2->z);
        $this->assertSame(4.0, $t2->w);
    }

    public function test_can_be_multiplied_by_a_scalar(): void
    {
        $t1 = Tuple::from(1.0, -2.0, 3.0, -4.0);

        $t2 = $t1->multiplyBy(3.5);

        $this->assertSame(3.5, $t2->x);
        $this->assertSame(-7.0, $t2->y);
        $this->assertSame(10.5, $t2->z);
        $this->assertSame(-14.0, $t2->w);
    }

    public function test_can_be_divided_by_a_scalar(): void
    {
        $t1 = Tuple::from(1.0, -2.0, 3.0, -4.0);

        $t2 = $t1->divideBy(2.0);

        $this->assertSame(0.5, $t2->x);
        $this->assertSame(-1.0, $t2->y);
        $this->assertSame(1.5, $t2->z);
        $this->assertSame(-2.0, $t2->w);
    }

    #[DataProvider('magnitudeProvider')]
    #[TestDox('The magnitude of vector ($x, $y, $z) is calculated to be $magnitude')]
    public function test_the_magnitude_of_a_vector_can_be_calculated(float $magnitude, float $x, float $y, float $z): void
    {
        $this->assertSame($magnitude, Tuple::vector($x, $y, $z)->magnitude());
    }

    #[DataProvider('normalizationProvider')]
    #[TestDox('The vector ($x1, $y1, $z1) is normalized to ($x2, $y2, $z2)')]
    public function test_a_vector_can_be_normalized(float $x1, float $y1, float $z1, float $x2, float $y2, float $z2): void
    {
        $n = Tuple::vector($x1, $y1, $z1)->normalize();

        $this->assertSame($x2, $n->x);
        $this->assertSame($y2, $n->y);
        $this->assertSame($z2, $n->z);
        $this->assertSame(1.0, $n->magnitude());
    }

    public function test_the_dot_product_of_two_vectors_can_be_calculated(): void
    {
        $a = Tuple::vector(1.0, 2.0, 3.0);
        $b = Tuple::vector(2.0, 3.0, 4.0);

        $this->assertSame(20.0, $a->dot($b));
    }

    public function test_the_cross_product_of_two_vectors_can_be_calculated(): void
    {
        $a = Tuple::vector(1.0, 2.0, 3.0);
        $b = Tuple::vector(2.0, 3.0, 4.0);

        $ab = $a->cross($b);
        $this->assertSame(-1.0, $ab->x);
        $this->assertSame(2.0, $ab->y);
        $this->assertSame(-1.0, $ab->z);

        $ba = $b->cross($a);
        $this->assertSame(1.0, $ba->x);
        $this->assertSame(-2.0, $ba->y);
        $this->assertSame(1.0, $ba->z);
    }

    public function test_two_tuples_can_be_compared(): void
    {
        $p1 = Tuple::point(0.0, 0.0, 0.0);
        $p2 = Tuple::point(1.0, 0.0, 0.0);
        $p3 = Tuple::point(0.0, 1.0, 0.0);
        $p4 = Tuple::point(0.0, 0.0, 1.0);

        $this->assertTrue($p1->equalTo($p1));
        $this->assertFalse($p1->equalTo($p2));
        $this->assertFalse($p1->equalTo($p3));
        $this->assertFalse($p1->equalTo($p4));
    }

    #[TestDox('Reflecting a vector approaching at 45°')]
    public function test_reflecting_a_vector_approaching_at_45_degrees(): void
    {
        $v = Tuple::vector(1, -1, 0);
        $n = Tuple::vector(0, 1, 0);

        $r = $v->reflect($n);

        $this->assertTrue($r->equalTo(Tuple::vector(1, 1, 0)));
    }

    public function test_reflecting_a_vector_off_a_slanted_surface(): void
    {
        $v = Tuple::vector(0, -1, 0);
        $n = Tuple::vector(sqrt(2) / 2, sqrt(2) / 2, 0);

        $r = $v->reflect($n);

        $this->assertTrue($r->equalTo(Tuple::vector(1, 0, 0)));
    }

    #[Property]
    public function test_addition_is_commutative_and_associative(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $a,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $b,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $c,
    ): void {
        $this->assertEquals($a->plus($b), $b->plus($a));
        $this->assertEquals($a->plus($b)->plus($c), $a->plus($b->plus($c)));
        $this->assertEquals($a, $a->plus(Tuple::vector(0, 0, 0)));
    }

    #[Property]
    public function test_subtraction_undoes_addition(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $a,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $b,
    ): void {
        $this->assertEquals($a, $a->plus($b)->minus($b));
        $this->assertEquals($a->minus($b), $a->plus($b->negate()));
    }

    #[Property]
    public function test_sums_and_differences_of_points_and_vectors_are_points_or_vectors(
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $p,
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $q,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $u,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $v,
    ): void {
        $this->assertTrue($p->plus($v)->isPoint());
        $this->assertTrue($v->plus($p)->isPoint());
        $this->assertTrue($u->plus($v)->isVector());
        $this->assertTrue($p->minus($q)->isVector());
        $this->assertTrue($p->minus($v)->isPoint());
        $this->assertTrue($u->minus($v)->isVector());
    }

    #[Property]
    public function test_no_point_can_be_added_to_a_point(
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $p,
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $q,
    ): void {
        $this->expectException(RuntimeException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $p->plus($q);
    }

    #[Property]
    public function test_no_point_can_be_subtracted_from_a_vector(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $v,
        #[ForAllExternal(RaytracerGenerators::class, 'points')]
        Tuple $p,
    ): void {
        $this->expectException(RuntimeException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $v->minus($p);
    }

    #[Property]
    public function test_negating_twice_gives_back_the_tuple_and_a_tuple_and_its_negation_cancel_out(
        #[ForAllExternal(RaytracerGenerators::class, 'tuples')]
        Tuple $t,
    ): void {
        $this->assertEquals($t, $t->negate()->negate());
        $this->assertEquals(Tuple::from(0, 0, 0, 0), $t->plus($t->negate()));
    }

    #[Property]
    public function test_multiplication_by_a_scalar_distributes_over_addition(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $a,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $b,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $factor,
    ): void {
        $this->assertEquals(
            $a->plus($b)->multiplyBy($factor),
            $a->multiplyBy($factor)->plus($b->multiplyBy($factor)),
        );
    }

    #[Property]
    public function test_division_by_a_scalar_undoes_multiplication_by_it(
        #[ForAllExternal(RaytracerGenerators::class, 'tuples')]
        Tuple $t,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroCoordinates')]
        float $factor,
    ): void {
        $this->assertEquals($t, $t->multiplyBy($factor)->divideBy($factor));
    }

    #[Property]
    public function test_no_tuple_can_be_divided_by_zero(
        #[ForAllExternal(RaytracerGenerators::class, 'tuples')]
        Tuple $t,
    ): void {
        $this->expectException(RuntimeException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $t->divideBy(0.0);
    }

    #[Property]
    public function test_the_dot_product_is_commutative_and_bilinear(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $a,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $b,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $c,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $factor,
    ): void {
        $this->assertSame($a->dot($b), $b->dot($a));
        $this->assertSame($a->dot($c) + $b->dot($c), $a->plus($b)->dot($c));
        $this->assertSame($factor * $a->dot($b), $a->multiplyBy($factor)->dot($b));
    }

    #[Property]
    public function test_the_cross_product_is_anticommutative_and_orthogonal_to_its_operands(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $a,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $b,
    ): void {
        $product = $a->cross($b);

        $this->assertTrue($product->isVector());
        $this->assertEquals($product, $b->cross($a)->negate());
        $this->assertEquals(Tuple::vector(0, 0, 0), $a->cross($a));
        $this->assertEqualsWithDelta(0.0, $product->dot($a), 0.0);
        $this->assertEqualsWithDelta(0.0, $product->dot($b), 0.0);
    }

    #[Property]
    public function test_the_magnitude_is_a_norm(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $a,
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $b,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $factor,
    ): void {
        $this->assertGreaterThanOrEqual(0.0, $a->magnitude());
        $this->assertEqualsWithDelta($a->dot($a), $a->magnitude() ** 2, 0.000001);
        $this->assertEqualsWithDelta(abs($factor) * $a->magnitude(), $a->multiplyBy($factor)->magnitude(), 0.000001);
        $this->assertLessThanOrEqual($a->magnitude() + $b->magnitude() + 0.000001, $a->plus($b)->magnitude());
    }

    #[Property]
    public function test_a_normalized_vector_has_magnitude_one_and_the_direction_of_the_vector(
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroVectors')]
        Tuple $v,
    ): void {
        $n = $v->normalize();

        $this->assertTrue($n->isVector());
        $this->assertEqualsWithDelta(1.0, $n->magnitude(), 0.000000000001);
        $this->assertEqualsWithDelta($n, $n->normalize(), 0.000000000001);
        $this->assertEqualsWithDelta($v, $n->multiplyBy($v->magnitude()), 0.000000001);
    }

    #[Property]
    #[TestWith([0.0, 0.0, 0.0, 2.0], 'only w is not zero')]
    public function test_a_normalized_tuple_has_magnitude_one(
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $x,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $y,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $z,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroCoordinates')]
        float $w,
    ): void {
        $this->assertEqualsWithDelta(1.0, Tuple::from($x, $y, $z, $w)->normalize()->magnitude(), 0.000000000001);
    }

    #[TestWith([0.0, 0.0, 0.0], 'zero vector')]
    #[TestWith([1e-200, 0.0, 0.0], 'magnitude underflows to zero')]
    #[TestWith([1e200, 0.0, 0.0], 'magnitude overflows to infinity')]
    #[TestWith([NAN, 0.0, 0.0], 'magnitude is not a number')]
    public function test_a_vector_without_a_usable_magnitude_cannot_be_normalized(float $x, float $y, float $z): void
    {
        $v = Tuple::vector($x, $y, $z);

        $this->expectException(RuntimeException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $v->normalize();
    }

    #[Property]
    public function test_reflecting_twice_gives_back_the_vector_and_reflection_preserves_the_magnitude(
        #[ForAllExternal(RaytracerGenerators::class, 'vectors')]
        Tuple $v,
        #[ForAllExternal(RaytracerGenerators::class, 'unitVectors')]
        Tuple $normal,
    ): void {
        $reflected = $v->reflect($normal);

        $this->assertEqualsWithDelta($v, $reflected->reflect($normal), 0.00000001);
        $this->assertEqualsWithDelta($v->magnitude(), $reflected->magnitude(), 0.00000001);
        $this->assertEqualsWithDelta(-$v->dot($normal), $reflected->dot($normal), 0.00000001);
    }

    /**
     * @param int<0, 3> $component
     */
    #[Property]
    public function test_tuples_are_equal_exactly_when_none_of_their_components_differ(
        #[ForAllExternal(RaytracerGenerators::class, 'tuples')]
        Tuple $t,
        int $component,
        #[ForAllExternal(RaytracerGenerators::class, 'nonZeroCoordinates')]
        float $difference,
    ): void {
        $components = [$t->x, $t->y, $t->z, $t->w];

        $same = Tuple::from(...$components);

        $components[$component] += $difference;

        $other = Tuple::from(...$components);

        $this->assertTrue($t->equalTo($same));
        $this->assertTrue($same->equalTo($t));
        $this->assertFalse($t->equalTo($other));
        $this->assertFalse($other->equalTo($t));
    }

    /**
     * @param positive-float $delta
     */
    #[Property]
    public function test_tuples_are_equal_when_their_components_differ_by_no_more_than_the_delta(
        #[ForAllExternal(RaytracerGenerators::class, 'tuples')]
        Tuple $t,
        #[ForAllExternal(RaytracerGenerators::class, 'positiveCoordinates')]
        float $delta,
        bool $negative,
    ): void {
        $difference = $negative ? -$delta : $delta;

        foreach ([[1, 0, 0, 0], [0, 1, 0, 0], [0, 0, 1, 0], [0, 0, 0, 1]] as [$x, $y, $z, $w]) {
            $other = Tuple::from(
                $t->x + $x * $difference,
                $t->y + $y * $difference,
                $t->z + $z * $difference,
                $t->w + $w * $difference,
            );

            $this->assertTrue($t->equalTo($other, $delta));
            $this->assertTrue($other->equalTo($t, $delta));
            $this->assertFalse($t->equalTo($other, $delta / 2));
            $this->assertFalse($other->equalTo($t, $delta / 2));
        }
    }

    #[Property]
    public function test_a_point_is_not_equal_to_the_vector_with_the_same_coordinates(
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $x,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $y,
        #[ForAllExternal(RaytracerGenerators::class, 'coordinates')]
        float $z,
    ): void {
        $this->assertFalse(Tuple::point($x, $y, $z)->equalTo(Tuple::vector($x, $y, $z)));
        $this->assertFalse(Tuple::vector($x, $y, $z)->equalTo(Tuple::point($x, $y, $z)));
    }

    #[Property]
    public function test_tuples_of_any_finite_floats_are_either_operated_on_or_rejected(float $x, float $y, float $z, float $w, float $factor): void
    {
        $t = Tuple::from($x, $y, $z, $w);

        $this->assertEquals($t, $t->negate()->negate());
        $this->assertGreaterThanOrEqual(0.0, $t->magnitude());
        $this->assertSame($t->dot($t), $t->multiplyBy(1.0)->dot($t));
        $this->assertTrue($t->cross($t)->isVector());

        try {
            $this->assertGreaterThan(0.0, $t->normalize()->magnitude());
        } catch (RuntimeException) {
            $this->assertFalse($t->magnitude() > 0.0 && is_finite($t->magnitude()));
        }

        try {
            /* @noinspection UnusedFunctionResultInspection */
            $t->divideBy($factor);

            $this->assertNotEqualsWithDelta(0.0, $factor, 0.0);
        } catch (RuntimeException) {
            $this->assertEqualsWithDelta(0.0, $factor, 0.0);
        }
    }
}
