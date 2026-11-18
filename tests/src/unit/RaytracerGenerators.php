<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function array_fill;
use function deg2rad;
use function max;
use Closure;
use PHPUnit\Framework\Attributes\GeneratorFor;
use PHPUnit\Framework\PropertyBased\Generators;
use PHPUnit\Framework\PropertyBased\ValueGenerator;

final class RaytracerGenerators
{
    /**
     * Multiples of 1/8 from -1024 to 1024.
     *
     * Sums and products of a few of these numbers are exact in floating-point
     * arithmetic, so that algebraic laws can be asserted without a tolerance.
     *
     * @return ValueGenerator<float>
     */
    public static function coordinates(): ValueGenerator
    {
        return Generators::integers(-8192, 8192)->map(
            static fn (int $eighths): float => $eighths / 8,
        );
    }

    /**
     * @return ValueGenerator<float>
     */
    public static function nonZeroCoordinates(): ValueGenerator
    {
        return Generators::oneOf(
            Generators::integers(1, 8192),
            Generators::integers(-8192, -1),
        )->map(
            static fn (int $eighths): float => $eighths / 8,
        );
    }

    /**
     * @return ValueGenerator<Tuple>
     */
    public static function points(): ValueGenerator
    {
        return Generators::combine(
            Tuple::point(...),
            self::coordinates(),
            self::coordinates(),
            self::coordinates(),
        );
    }

    /**
     * @return ValueGenerator<Tuple>
     */
    public static function vectors(): ValueGenerator
    {
        return Generators::combine(
            Tuple::vector(...),
            self::coordinates(),
            self::coordinates(),
            self::coordinates(),
        );
    }

    /**
     * @return ValueGenerator<Tuple>
     */
    public static function nonZeroVectors(): ValueGenerator
    {
        return self::vectors()->filter(
            static fn (Tuple $vector): bool => $vector->magnitude() > 0.0,
        );
    }

    /**
     * @return ValueGenerator<Tuple>
     */
    public static function unitVectors(): ValueGenerator
    {
        return self::nonZeroVectors()->map(
            static fn (Tuple $vector): Tuple => $vector->normalize(),
        );
    }

    /**
     * Tuples that are neither necessarily points nor necessarily vectors.
     *
     * @return ValueGenerator<Tuple>
     */
    public static function tuples(): ValueGenerator
    {
        return Generators::combine(
            Tuple::from(...),
            self::coordinates(),
            self::coordinates(),
            self::coordinates(),
            self::coordinates(),
        );
    }

    /**
     * Colors with channels that are multiples of 1/256 from -0.5 to 1.5, so
     * that channels outside of the displayable range are generated as well.
     *
     * @return ValueGenerator<Color>
     */
    #[GeneratorFor(Color::class)]
    public static function colors(): ValueGenerator
    {
        return Generators::combine(
            Color::from(...),
            self::channels(),
            self::channels(),
            self::channels(),
        );
    }

    /**
     * Colors with channels that are multiples of 1/256 from 0 to 1.
     *
     * @return ValueGenerator<Color>
     */
    public static function displayableColors(): ValueGenerator
    {
        $channels = Generators::integers(0, 256)->map(
            static fn (int $fraction): float => $fraction / 256,
        );

        return Generators::combine(
            Color::from(...),
            $channels,
            $channels,
            $channels,
        );
    }

    /**
     * Materials with a displayable color, with ambient, diffuse, and specular
     * reflection that are multiples of 1/8 from 0 to 1, and with a shininess
     * from 1 to 300.
     *
     * @return ValueGenerator<Material>
     */
    #[GeneratorFor(Material::class)]
    public static function materials(): ValueGenerator
    {
        $reflection = Generators::integers(0, 8)->map(
            static fn (int $eighths): float => $eighths / 8,
        );

        return Generators::combine(
            Material::from(...),
            self::displayableColors(),
            $reflection,
            $reflection,
            $reflection,
            self::integersAsFloats(1, 300),
        );
    }

    /**
     * Rows of a square matrix of integers from -16 to 16.
     *
     * Products and determinants of matrices of such integers are exact in
     * floating-point arithmetic.
     *
     * @param int<1, 4> $size
     *
     * @return ValueGenerator<list<list<float>>>
     */
    public static function rowsOfSize(int $size): ValueGenerator
    {
        return Generators::lists(
            Generators::lists(self::integersAsFloats(-16, 16), $size, $size),
            $size,
            $size,
        );
    }

    /**
     * @param int<1, 4> $size
     *
     * @return ValueGenerator<Matrix>
     */
    public static function matricesOfSize(int $size): ValueGenerator
    {
        return self::rowsOfSize($size)->map(Matrix::fromArray(...));
    }

    /**
     * Square matrices that are invertible by construction: each is the
     * product of a lower triangular matrix with ones on its diagonal and an
     * upper triangular matrix without a zero on its diagonal.
     *
     * @param int<1, 4> $size
     *
     * @return ValueGenerator<Matrix>
     */
    public static function invertibleMatricesOfSize(int $size): ValueGenerator
    {
        return Generators::combine(
            /**
             * @param list<list<float>> $elements
             * @param list<float>       $diagonal
             */
            static function (array $elements, array $diagonal) use ($size): Matrix
            {
                $lower = [];
                $upper = [];

                for ($i = 0; $i < $size; $i++) {
                    for ($j = 0; $j < $size; $j++) {
                        $lower[$i][$j] = 0.0;
                        $upper[$i][$j] = 0.0;

                        if ($i === $j) {
                            $lower[$i][$j] = 1.0;
                            $upper[$i][$j] = $diagonal[$i];
                        } elseif ($i > $j) {
                            $lower[$i][$j] = $elements[$i][$j];
                        } else {
                            $upper[$i][$j] = $elements[$i][$j];
                        }
                    }
                }

                return Matrix::fromArray($lower)->multiply(Matrix::fromArray($upper));
            },
            Generators::lists(
                Generators::lists(self::integersAsFloats(-3, 3), $size, $size),
                $size,
                $size,
            ),
            Generators::lists(
                Generators::oneOf(
                    self::integersAsFloats(1, 3),
                    self::integersAsFloats(-3, -1),
                ),
                $size,
                $size,
            ),
        );
    }

    /**
     * Products of one to four translations, scalings, rotations, and
     * shearings. These matrices are invertible by construction.
     *
     * @return ValueGenerator<Matrix>
     */
    public static function transformations(): ValueGenerator
    {
        return Generators::lists(
            Generators::oneOf(
                self::translations(),
                self::scalings(),
                self::rotations(),
                self::shearings(),
            ),
            1,
            4,
        )->map(self::product(...));
    }

    /**
     * Products of one to four translations and rotations. These
     * transformations preserve distances and angles.
     *
     * @return ValueGenerator<Matrix>
     */
    public static function rigidTransformations(): ValueGenerator
    {
        return Generators::lists(
            Generators::oneOf(
                self::translations(),
                self::rotations(),
            ),
            1,
            4,
        )->map(self::product(...));
    }

    /**
     * @return ValueGenerator<Matrix>
     */
    public static function translations(): ValueGenerator
    {
        return Generators::combine(
            Transformations::translation(...),
            self::offsets(),
            self::offsets(),
            self::offsets(),
        );
    }

    /**
     * @return ValueGenerator<Matrix>
     */
    public static function scalings(): ValueGenerator
    {
        return Generators::combine(
            Transformations::scaling(...),
            self::scalingFactors(),
            self::scalingFactors(),
            self::scalingFactors(),
        );
    }

    /**
     * Rotations around one of the axes by a whole number of degrees.
     *
     * @return ValueGenerator<Matrix>
     */
    public static function rotations(): ValueGenerator
    {
        return Generators::combine(
            static fn (string $axis, int $degrees): Matrix => match ($axis) {
                'x'     => Transformations::rotationAroundX(deg2rad($degrees)),
                'y'     => Transformations::rotationAroundY(deg2rad($degrees)),
                default => Transformations::rotationAroundZ(deg2rad($degrees)),
            },
            Generators::elements('x', 'y', 'z'),
            Generators::integers(-360, 360),
        );
    }

    /**
     * Shearings that move one coordinate in proportion to one other
     * coordinate.
     *
     * @return ValueGenerator<Matrix>
     */
    public static function shearings(): ValueGenerator
    {
        return Generators::combine(
            static function (int $position, float $factor): Matrix
            {
                $factors            = array_fill(0, 6, 0.0);
                $factors[$position] = $factor;

                return Transformations::shearing(...$factors);
            },
            Generators::integers(0, 5),
            Generators::integers(-16, 16)->map(
                static fn (int $eighths): float => $eighths / 8,
            ),
        );
    }

    /**
     * @return ValueGenerator<Ray>
     */
    public static function rays(): ValueGenerator
    {
        return Generators::combine(
            Ray::from(...),
            self::points(),
            self::nonZeroVectors(),
        );
    }

    /**
     * Points with coordinates that are multiples of 1/8 from -8 to 8.
     *
     * @return ValueGenerator<Tuple>
     */
    public static function nearPoints(): ValueGenerator
    {
        return Generators::combine(
            Tuple::point(...),
            self::nearCoordinates(),
            self::nearCoordinates(),
            self::nearCoordinates(),
        );
    }

    /**
     * Vectors with coordinates that are multiples of 1/8 from -8 to 8.
     *
     * @return ValueGenerator<Tuple>
     */
    public static function nearNonZeroVectors(): ValueGenerator
    {
        return Generators::combine(
            Tuple::vector(...),
            self::nearCoordinates(),
            self::nearCoordinates(),
            self::nearCoordinates(),
        )->filter(
            static fn (Tuple $vector): bool => $vector->magnitude() > 0.0,
        );
    }

    /**
     * Points with coordinates that are multiples of 1/8 from -0.5 to 0.5.
     * The distance of such a point from the origin is less than 0.87.
     *
     * @return ValueGenerator<Tuple>
     */
    public static function pointsInsideTheUnitSphere(): ValueGenerator
    {
        $coordinates = Generators::integers(-4, 4)->map(
            static fn (int $eighths): float => $eighths / 8,
        );

        return Generators::combine(
            Tuple::point(...),
            $coordinates,
            $coordinates,
            $coordinates,
        );
    }

    /**
     * Rays that pass through a point inside the unit sphere. Some of them
     * start inside the unit sphere, most of them start outside of it, in
     * front of it or behind it.
     *
     * @return ValueGenerator<Ray>
     */
    public static function raysThroughTheUnitSphere(): ValueGenerator
    {
        return Generators::combine(
            static fn (Tuple $inside, Tuple $direction, float $t): Ray => Ray::from(
                $inside->minus($direction->multiplyBy($t)),
                $direction,
            ),
            self::pointsInsideTheUnitSphere(),
            self::nearNonZeroVectors(),
            Generators::integers(-16, 16)->map(
                static fn (int $eighths): float => $eighths / 8,
            ),
        );
    }

    /**
     * Rays of which about half pass through the unit sphere. Most of the
     * others miss it.
     *
     * @return ValueGenerator<Ray>
     */
    public static function raysNearTheUnitSphere(): ValueGenerator
    {
        return Generators::oneOf(
            self::raysThroughTheUnitSphere(),
            Generators::combine(
                Ray::from(...),
                self::nearPoints(),
                self::nearNonZeroVectors(),
            ),
        );
    }

    /**
     * Canvases of up to 8 x 8 pixels with a background color and up to twelve
     * pixels that were written to them.
     *
     * @return ValueGenerator<Canvas>
     */
    #[GeneratorFor(Canvas::class)]
    public static function canvases(): ValueGenerator
    {
        return Generators::combine(
            static fn (int $width, int $height) => [$width, $height],
            self::positiveIntegersUpTo(8),
            self::positiveIntegersUpTo(8),
        )->flatMap(
            static fn (array $size): ValueGenerator => self::canvasesOfSize($size[0], $size[1]),
        );
    }

    /**
     * Multiples of 1/2 from -4 to 4. Lists of these numbers often contain a
     * number more than once, zero, and numbers of either sign.
     *
     * @return ValueGenerator<list<float>>
     */
    public static function intersectionTimes(): ValueGenerator
    {
        return Generators::lists(
            Generators::integers(-8, 8)->map(
                static fn (int $halves): float => $halves / 2,
            ),
            0,
            8,
        );
    }

    /**
     * Multiples of 1/8 from 0.25 to 4 and from -4 to -0.25.
     *
     * @return ValueGenerator<float>
     */
    public static function scalingFactors(): ValueGenerator
    {
        return Generators::oneOf(
            Generators::integers(2, 32),
            Generators::integers(-32, -2),
        )->map(
            static fn (int $eighths): float => $eighths / 8,
        );
    }

    /**
     * @param positive-int $width
     * @param positive-int $height
     *
     * @return ValueGenerator<Canvas>
     */
    private static function canvasesOfSize(int $width, int $height): ValueGenerator
    {
        return Generators::combine(
            static function (Color $background, array $writes) use ($width, $height): Canvas
            {
                $canvas = Canvas::from($width, $height, $background);

                foreach ($writes as $write) {
                    $write($canvas);
                }

                return $canvas;
            },
            self::colors(),
            Generators::lists(
                Generators::combine(
                    self::write(...),
                    self::positiveIntegersUpTo($width),
                    self::positiveIntegersUpTo($height),
                    self::colors(),
                ),
                0,
                12,
            ),
        );
    }

    /**
     * @param positive-int $x
     * @param positive-int $y
     *
     * @return Closure(Canvas): void
     */
    private static function write(int $x, int $y, Color $color): Closure
    {
        return static function (Canvas $canvas) use ($x, $y, $color): void
        {
            $canvas->writePixel($x, $y, $color);
        };
    }

    /**
     * @param positive-int $maximum
     *
     * @return ValueGenerator<positive-int>
     */
    private static function positiveIntegersUpTo(int $maximum): ValueGenerator
    {
        return Generators::integers(1, $maximum)->map(
            static fn (int $integer) => max(1, $integer),
        );
    }

    /**
     * Multiples of 1/8 from -8 to 8.
     *
     * @return ValueGenerator<float>
     */
    private static function nearCoordinates(): ValueGenerator
    {
        return Generators::integers(-64, 64)->map(
            static fn (int $eighths): float => $eighths / 8,
        );
    }

    /**
     * Multiples of 1/8 from -10 to 10.
     *
     * @return ValueGenerator<float>
     */
    private static function offsets(): ValueGenerator
    {
        return Generators::integers(-80, 80)->map(
            static fn (int $eighths): float => $eighths / 8,
        );
    }

    /**
     * @return ValueGenerator<float>
     */
    private static function integersAsFloats(int $minimum, int $maximum): ValueGenerator
    {
        return Generators::integers($minimum, $maximum)->map(
            static fn (int $integer): float => (float) $integer,
        );
    }

    /**
     * @param list<Matrix> $matrices
     */
    private static function product(array $matrices): Matrix
    {
        $product = Matrix::identity(4);

        foreach ($matrices as $matrix) {
            $product = $product->multiply($matrix);
        }

        return $product;
    }

    /**
     * @return ValueGenerator<float>
     */
    private static function channels(): ValueGenerator
    {
        return Generators::integers(-128, 384)->map(
            static fn (int $fraction): float => $fraction / 256,
        );
    }
}
