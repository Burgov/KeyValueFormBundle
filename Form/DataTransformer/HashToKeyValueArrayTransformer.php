<?php
namespace Burgov\Bundle\KeyValueFormBundle\Form\DataTransformer;

use Burgov\Bundle\KeyValueFormBundle\KeyValueContainer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class HashToKeyValueArrayTransformer implements DataTransformerInterface
{

    private $useContainerObject;

    /**
     * @param bool $useContainerObject Whether to return a KeyValueContainer object or simply an array
     */
    public function __construct($useContainerObject)
    {
        $this->useContainerObject = $useContainerObject;
    }

    /**
     * Doing the transformation here would be too late for the collection type to do it's resizing magic, so
     * instead it is done in the forms PRE_SET_DATA listener
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * @param mixed $value
     * @return KeyValueContainer|array
     * @throws \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function reverseTransform($value)
    {
        $return = $this->useContainerObject ? new KeyValueContainer() : array();

        foreach ($value as $data) {
            if (array('key', 'value') != array_keys($data)) {
                throw new TransformationFailedException;
            }

            if (isset($data['key'], $return[$data['key']])) {
                throw new TransformationFailedException('Duplicate key detected');
            }

            $return[$data['key']] = $data['value'];
        }

        return $return;
    }

} 