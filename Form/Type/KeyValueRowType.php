<?php

namespace Burgov\Bundle\KeyValueFormBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class KeyValueRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array('string' => 'text', 'integer' => 'integer', 'boolean' => 'checkbox');

        $builder->add('key');
        $builder->add('type', 'choice', array(
            'choices' => array_combine(array_keys($choices), array_keys($choices))
        ));

        $addValueField = function (FormInterface $form, $type) {
            $form->add('value', $type, array('data' => null));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $e) use ($addValueField, $choices) {
            $data = $e->getData();
            if (null === $data) {
                return;
            }

            $type = $choices[$data['type']];

            $addValueField($e->getForm(), $type);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $e) use ($addValueField, $choices) {
            $data = $e->getData();

            // todo: check that this is actually one of the allowed types
            $type = $choices[$data['type']];

            $addValueField($e->getForm(), $type);
        });
    }

    public function getName()
    {
        return 'burgov_key_value_row';
    }
} 