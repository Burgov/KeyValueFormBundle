<?php

namespace Burgov\Bundle\KeyValueFormBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyValueRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('key');
        $builder->add('value', $options['value_type'], $options['value_options']);
    }

    public function getName()
    {
        return 'burgov_key_value_row';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'value_options' => array()
        ));

        $resolver->setRequired(array('value_type'));
    }


} 