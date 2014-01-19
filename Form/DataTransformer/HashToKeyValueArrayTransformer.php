<?php
namespace Burgov\Bundle\KeyValueFormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class HashToKeyValueArrayTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // Doing the transformation here would be too late for the collection type to do it's resizing magic, so
        // instead it is done in the forms PRE_SET_DATA listener
        return $value;
    }

    public function reverseTransform($value)
    {
        $return = array();

        foreach ($value as $data) {
            if (array('key', 'value') != array_keys($data)) {
                continue;
            }

            $return[$data['key']] = $data['value'];
        }

        return $return;
    }

} 