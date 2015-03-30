<?php

namespace Burgov\Bundle\KeyValueFormBundle\Form\Type;

use Burgov\Bundle\KeyValueFormBundle\Form\DataTransformer\HashToKeyValueArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new HashToKeyValueArrayTransformer($options['use_container_object']));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $e) {
            $input = $e->getData();

            if (null === $input) {
                return;
            }

            $output = array();

            foreach ($input as $key => $value) {
                $output[] = array(
                    'key' => $key,
                    'value' => $value
                );
            }

            $e->setData($output);
        }, 1);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'type' => 'burgov_key_value_row',
            'allow_add' => true,
            'allow_delete' => true,
            'key_options' => array(),
            'value_options' => array(),
            'allowed_keys' => null,
            'use_container_object' => false,
            'options' => function(Options $options) {
                return array(
                    'value_type' => $options['value_type'],
                    'key_options' => $options['key_options'],
                    'value_options' => $options['value_options'],
                    'allowed_keys' => $options['allowed_keys']
                );
            }
        ));

        $resolver->setRequired(array('value_type'));
        $resolver->setAllowedTypes(array('allowed_keys' => array('null', 'array')));
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