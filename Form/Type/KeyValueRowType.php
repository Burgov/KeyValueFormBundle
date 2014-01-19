<?php

namespace Burgov\Bundle\KeyValueFormBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyValueRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (null === $options['allowed_keys']) {
            $builder->add('key', 'text', array(
            ));
        } else {
            $builder->add('key', 'choice', array(
                'choice_list' => new SimpleChoiceList($options['allowed_keys'])
            ));
        }

        $builder->add('value', $options['value_type'], $options['value_options']);
    }

    public function getName()
    {
        return 'burgov_key_value_row';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'value_options' => array(),
            'allowed_keys' => null
        ));

        $resolver->setRequired(array('value_type'));
        $resolver->setAllowedTypes(array('allowed_keys' => array('null', 'array')));
    }


} 