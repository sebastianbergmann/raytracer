<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class World
{
    private ObjectCollection $objects;

    private ?PointLight $light = null;

    public static function default(): self
    {
        $light = PointLight::from(
            Tuple::point(-10, 10, -10),
            Color::from(1, 1, 1)
        );

        $s1 = new Sphere;
        $s1->setMaterial(Material::from(Color::from(0.8, 1.0, 0.6), 0.1, 0.7, 0.2, 200.0));

        $s2 = new Sphere;
        $s2->setTransformation(Matrix::scaling(0.5, 0.5, 0.5));

        $objects = new ObjectCollection;
        $objects->add($s1);
        $objects->add($s2);

        return new self($objects, $light);
    }

    public function __construct(ObjectCollection $objects = null, PointLight $light = null)
    {
        if ($objects === null) {
            $objects = new ObjectCollection;
        }

        $this->objects = $objects;
        $this->light   = $light;
    }

    public function objects(): ObjectCollection
    {
        return $this->objects;
    }

    /**
     * @throws WorldHasNoLightException
     */
    public function light(): PointLight
    {
        if ($this->light === null) {
            throw new WorldHasNoLightException;
        }

        return $this->light;
    }

    public function intersect(Ray $r): IntersectionCollection
    {
        $intersections = IntersectionCollection::from();

        foreach ($this->objects as $object) {
            $intersections = $intersections->merge($object->intersect($r));
        }

        return $intersections;
    }
}
