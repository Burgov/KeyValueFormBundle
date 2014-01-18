<?php

namespace Burgov\Bundle\KeyValueFormBundle\Form\Type;

use Burgov\Bundle\KeyValueFormBundle\Form\DataTransformer\HashToKeyValueArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new HashToKeyValueArrayTransformer());

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $e) {
            $input = $e->getData();
            $output = array();

            foreach ($input as $key => $value) {
                switch (gettype($value)) {
                    case 'string':
                    case 'integer':
                    case 'boolean':
                        $type = gettype($value);
                        break;
                    default:
                        throw new TransformationFailedException('Unsupported data type ' . gettype($value));
                }

                $output[] = array(
                    'key' => $key,
                    'type' => $type,
                    'value' => $value
                );
            }

            $e->setData($output);
        }, 1);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'type' => new KeyValueRowType(),
            'allow_add' => true,
            'allow_delete' => true
        ));
    }

    public function getParent()
    {
        return 'collection';
    }

    public function getName()
    {
        return 'burgov_key_value';
    }
}