<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function sqrt;

final class Sphere implements Object_
{
    private Tuple $origin;

    private float $radius = 1.0;

    private Matrix $transformation;

    private Material $material;

    public function __construct()
    {
        $this->origin         = Tuple::point(0, 0, 0);
        $this->transformation = Matrix::identity(4);
        $this->material       = Material::default();
    }

    public function origin(): Tuple
    {
        return $this->origin;
    }

    public function radius(): float
    {
        return $this->radius;
    }

    public function setTransformation(Matrix $transformation): void
    {
        $this->transformation = $transformation;
    }

    public function transformation(): Matrix
    {
        return $this->transformation;
    }

    public function setMaterial(Material $material): void
    {
        $this->material = $material;
    }

    public function material(): Material
    {
        return $this->material;
    }

    /**
     * @throws RuntimeException
     */
    public function intersect(Ray $r): IntersectionCollection
    {
        $r = $r->transform($this->transformation->inverse());

        $sphereToRay = $r->origin()->minus($this->origin);

        $a = $r->direction()->dot($r->direction());
        $b = 2 * $r->direction()->dot($sphereToRay);
        $c = $sphereToRay->dot($sphereToRay) - 1;

        $discrimiant = $b ** 2 - 4 * $a * $c;

        if ($discrimiant < 0) {
            return IntersectionCollection::from();
        }

        $t1 = (-$b - sqrt($discrimiant)) / (2 * $a);
        $t2 = (-$b + sqrt($discrimiant)) / (2 * $a);

        return IntersectionCollection::from(
            Intersection::from($t1, $this),
            Intersection::from($t2, $this)
        );
    }

    /**
     * @throws RuntimeException
     * @psalm-mutation-free
     */
    public function normalAt(Tuple $worldPoint): Tuple
    {
        $objectPoint  = $this->transformation->inverse()->multiplyBy($worldPoint);
        $objectNormal = $objectPoint->minus($this->origin);

        $worldNormal = $this->transformation->inverse()->transpose()->multiplyBy($objectNormal);

        return Tuple::vector($worldNormal->x(), $worldNormal->y(), $worldNormal->z())->normalize();
    }
}
