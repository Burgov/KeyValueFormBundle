<?php
namespace Burgov\Bundle\KeyValueFormBundle\Form\DataTransformer;

use Burgov\Bundle\KeyValueFormBundle\KeyValueContainer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class HashToKeyValueArrayTransformer implements DataTransformerInterface
{

    /**
     * Doing the transformation here would be too late for the collection type to do it's resizing magic, so
     * instead it is done in the forms PRE_SET_DATA listener
     */
    public function transform($value)
    {
        return $value;
    }

    public function reverseTransform($value)
    {
        $return = new KeyValueContainer();

        foreach ($value as $data) {
            if (array('key', 'value') != array_keys($data)) {
                throw new TransformationFailedException;
            }

            if (array_key_exists($data['key'], $return)) {
                throw new TransformationFailedException('Duplicate key detected');
            }

            $return[$data['key']] = $data['value'];
        }

        return $return;
    }

} 