<?php

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
        $this->path  = $path;
    }

    /** {@inheritdoc} */
    public function transform($entity)
    {

        if (null === $entity) {
            return ['storage' => null, 'helper' => ''];
        }

        $class = $this->class;

        if (!($entity instanceof $class)) {
            throw new TransformationFailedException('Transformed entity is not instanse of '.$this->class);
        }

        $accessor = new PropertyAccessor();

        return ['storage' => $entity, 'helper' => $accessor->getValue($entity, $this->path)];
    }

    /** {@inheritdoc} */
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
            throw new TransformationFailedException('Transformed entity is not instanse of '.$this->class);
        }

        return $array['storage'];
    }
}
