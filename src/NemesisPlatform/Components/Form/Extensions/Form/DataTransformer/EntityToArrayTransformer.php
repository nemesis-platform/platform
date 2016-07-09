<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 10.12.2014
 * Time: 12:44
 */

namespace NemesisPlatform\Components\Form\Extensions\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class EntityToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $path;

    public function __construct($class, $path)
    {
        $this->class = $class;
        $this->path = $path;
    }

    /**
     * Transforms Entity to Entity,Label pair
     * @param $entity
     * @return array
     * @throws TransformationFailedException
     */
    public function transform($entity)
    {

        if (null === $entity) {
            return array('storage' => null, 'helper' => '');
        }

        $class = $this->class;

        if (!($entity instanceof $class)) {
            throw new TransformationFailedException('Transformed entity is not instanse of ' . $this->class);
        }

        $accessor = new PropertyAccessor();

        return array('storage' => $entity, 'helper' => $accessor->getValue($entity, $this->path));
    }

    /**
     * Transforms Entity,Label to Entity
     * @param $array
     * @return null|string
     * @throws TransformationFailedException
     */
    public function reverseTransform($array)
    {
        if (!is_array($array) || !array_key_exists('storage', $array)) {
            return null;
        }

        if ($array['storage'] === null) {
            return null;
        }

        $class = $this->class;

        if (!($array['storage'] instanceof $class)) {
            throw new TransformationFailedException('Transformed entity is not instanse of ' . $this->class);
        }

        return $array['storage'];
    }
}
