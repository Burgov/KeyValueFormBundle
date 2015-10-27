<?php

namespace Burgov\Bundle\KeyValueFormBundle\Form\Type;

use Burgov\Bundle\KeyValueFormBundle\Form\DataTransformer\HashToKeyValueArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        $this->configureOptions($resolver);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // check if Form component version 2.8+ is used
        $isSf28 = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');

        $resolver->setDefaults(array(
            $isSf28 ? 'entry_type' : 'type' => 'burgov_key_value_row',
            'allow_add' => true,
            'allow_delete' => true,
            'key_type' => 'text',
            'key_options' => array(),
            'value_options' => array(),
            'allowed_keys' => null,
            'use_container_object' => false,
            $isSf28 ? 'entry_options' : 'options' => function(Options $options) {
                return array(
                    'key_type' => $options['key_type'],
                    'value_type' => $options['value_type'],
                    'key_options' => $options['key_options'],
                    'value_options' => $options['value_options'],
                    'allowed_keys' => $options['allowed_keys']
                );
            }
        ));

        $resolver->setRequired(array('value_type'));

        if (method_exists($resolver, 'setDefined')) {
            // Symfony 2.6+ API
            $resolver->setAllowedTypes('allowed_keys' => array('null', 'array'));
        } else {
            // Symfony <2.6 API
            $resolver->setAllowedTypes(array('allowed_keys' => array('null', 'array')));
        }
    }

    public function getParent()
    {
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? 'Symfony\Component\Form\Extension\Core\Type\CollectionType' : 'collection';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'burgov_key_value';
    }
}
