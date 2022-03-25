<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

abstract class Shape
{
    private Matrix $transform;
    private Material $material;

    public static function default(): static
    {
        return self::from(
            Matrix::identity(4),
            Material::default()
        );
    }

    /**
     * @psalm-suppress UnsafeInstantiation
     */
    public static function from(Matrix $transform, Material $material): static
    {
        return new static($transform, $material);
    }

    protected function __construct(Matrix $transform, Material $material)
    {
        $this->transform = $transform;
        $this->material  = $material;
    }

    public function transform(): Matrix
    {
        return $this->transform;
    }

    public function setTransform(Matrix $transform): void
    {
        $this->transform = $transform;
    }

    public function material(): Material
    {
        return $this->material;
    }

    public function setMaterial(Material $material): void
    {
        $this->material = $material;
    }

    /**
     * @psalm-mutation-free
     *
     * @throws RuntimeException
     */
    public function intersect(Ray $ray): IntersectionCollection
    {
        $localRay = $ray->transform($this->transform->inverse());

        return $this->localIntersect($localRay);
    }

    /**
     * @psalm-mutation-free
     *
     * @throws RuntimeException
     */
    public function normalAt(Tuple $worldPoint): Tuple
    {
        $localPoint  = $this->transform->inverse()->multiplyBy($worldPoint);
        $localNormal = $this->localNormalAt($localPoint);
        $worldNormal = $this->transform->inverse()->transpose()->multiplyBy($localNormal);

        return Tuple::vector($worldNormal->x(), $worldNormal->y(), $worldNormal->z())->normalize();
    }

    /**
     * @psalm-mutation-free
     */
    abstract public function localIntersect(Ray $ray): IntersectionCollection;

    /**
     * @psalm-mutation-free
     */
    abstract public function localNormalAt(Tuple $point): Tuple;
}
