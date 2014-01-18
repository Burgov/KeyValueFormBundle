<?php
namespace Burgov\Bundle\KeyValueFormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

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
            $return[$data['key']] = $data['value'];
        }

        return $return;
    }

} 