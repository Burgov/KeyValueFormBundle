<?php

namespace Burgov\Bundle\KeyValueFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyValueRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (null === $options['allowed_keys']) {
            $builder->add('key', $options['key_type'], $options['key_options']);
        } else {
            $builder->add('key', 'choice', array_merge(array(
                'choice_list' => new SimpleChoiceList($options['allowed_keys'])
            ), $options['key_options']
            ));
        }

        $builder->add('value', $options['value_type'], $options['value_options']);
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'burgov_key_value_row';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'key_type' => 'text',
            'key_options' => array(),
            'value_options' => array(),
            'allowed_keys' => null
        ));

        $resolver->setRequired(array('value_type'));

        if (method_exists($resolver, 'setDefined')) {
            // Symfony 2.6+ API
            $resolver->setAllowedTypes('allowed_keys', array('null', 'array'));
        } else {
            // Symfony <2.6 API
            $resolver->setAllowedTypes(array('allowed_keys' => array('null', 'array')));
        }
    }
}
