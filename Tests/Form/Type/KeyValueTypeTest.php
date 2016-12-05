<?php

namespace Burgov\Bundle\KeyValueFormBundle\Tests\Form\Type;

use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueRowType;
use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType;
use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\Test\TypeTestCase;

class KeyValueTypeTest extends TypeTestCase
{
    private static $isSf28;
    private static $typeNames = array();

    public function setUp()
    {
        parent::setUp();

        if (null === self::$isSf28) {
            self::$isSf28 = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
            if (self::$isSf28) {
                self::$typeNames = array(
                    'choice' => 'Symfony\Component\Form\Extension\Core\Type\ChoiceType',
                    'text' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
                    'burgov_key_value' => 'Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType',
                );
            } else {
                self::$typeNames = array(
                    'choice' => 'choice',
                    'text' => 'text',
                    'burgov_key_value' => 'burgov_key_value',
                );
            }
        }
    }

    public function getExtensions()
    {
        return array(new ConcreteExtension());
    }

    public function testSubmitValidData()
    {
        $originalData = array(
            'old-key1' => 'old-string-value1',
            'old-key2' => 'old-string-value2',
        );

        $submitData = array(
            array(
                'key' => 'key1',
                'value' => 'string-value'
            ),
            array(
                'key' => 'key2',
                'value' => '5'
            ),
            array(
                'key' => 'key3',
                'value' => '1'
            )
        );

        $expectedData = array(
            'key1' => array('key1' => 'string-value'),
            'key2' => array('key2' => '5'),
            'key3' => array('key3' => '1'),
        );

        $form = $this->factory->create(
            self::$typeNames['burgov_key_value'],
            $originalData,
            array(
                'value_type' => self::$typeNames['text'],
                'key_options' => array('label' => 'label_key'),
                'value_options' => array('label' => 'label_value')
            )
        );

        $this->assertFormTypes(array(self::$typeNames['text'], self::$typeNames['text']), array(self::$typeNames['text'], self::$typeNames['text']), $form);
        $this->assertFormOptions(array(array('label' => 'label_key'), array('label' => 'label_value')), $form);

        $form->submit($submitData);
        $this->assertTrue($form->isValid(), $form->getErrors());

        $this->assertFormTypes(array(self::$typeNames['text'], self::$typeNames['text'], self::$typeNames['text']), array(self::$typeNames['text'], self::$typeNames['text'], self::$typeNames['text']), $form);
        $this->assertFormOptions(array(array('label' => 'label_key'), array('label' => 'label_value')), $form);

        $this->assertSame($expectedData, $form->getData());
    }

    public function testWithChoiceType()
    {
        $obj1 = new \StdClass();
        $obj1->id = 1;
        $obj1->name = 'choice1';

        $obj2 = new \StdClass();
        $obj2->id = 2;
        $obj2->name = 'choice2';

        $valueOptions = array('label' => 'label_value');
        if (self::$isSf28) {
            // Symfony 2.8+
            $valueOptions['choices'] = array($obj1, $obj2);
            $valueOptions['choice_name'] = 'id';
            $valueOptions['choice_value'] = 'name';
        } else {
            // Symfony <2.8
            $valueOptions['choice_list'] = new ObjectChoiceList(array($obj1, $obj2), 'name', array(), null, 'id');
        }

        $form = $this->factory->create(
            self::$typeNames['burgov_key_value'],
            null,
            array(
                'value_type' => self::$typeNames['choice'],
                'key_options' => array('label' => 'label_key'),
                'value_options' => $valueOptions,
            )
        );

        $this->assertFormTypes(array(), array(), $form);
        $this->assertFormOptions(array(array('label' => 'label_key'), array('label' => 'label_value')), $form);

        $form->submit(array(
            array(
                'key' => 'key1',
                'value' => '2'
            ),
            array(
                'key' => 'key2',
                'value' => '1'
            )
        ));

        $this->assertFormTypes(array(self::$typeNames['text'], self::$typeNames['text']), array(self::$typeNames['choice'], self::$typeNames['choice']), $form);
        $this->assertFormOptions(array(array('label' => 'label_key'), array('label' => 'label_value')), $form);

        $this->assertTrue($form->isValid());

        $this->assertSame(array('key1' => $obj2, 'key2' => $obj1), $form->getData());
    }

    public function testWithCustomKeyType()
    {
        $form = $this->factory->create(self::$typeNames['burgov_key_value'], null, array(
            'key_type' => 'country',
            'value_type' => 'integer',
            'key_options' => array('label' => 'label_key'),
        ));

        $this->assertFormTypes(array(), array(), $form);
        $this->assertFormOptions(array(array('label' => 'label_key'), array()), $form);

        $form->submit(array(
            array(
                'key' => 'GB',
                'value' => '2'
            ),
            array(
                'key' => 'CZ',
                'value' => '1'
            )
        ));

        $this->assertFormTypes(array('country', 'country'), array('integer', 'integer'), $form);
        $this->assertFormOptions(array(array('label' => 'label_key'), array()), $form);

        $this->assertTrue($form->isValid());

        $this->assertSame(array('GB' => 2, 'CZ' => 1), $form->getData());
    }

    private function assertFormTypes(array $keys, array $values, $form)
    {
        $this->assertCount(count($values), $form);
        for ($i = 0; $i < count($form); $i++) {
            if (isset($keys[$i])) {
                
                $this->assertInstanceOf($keys[$i], $form->get($i)->get('key')->getConfig()->getType()->getInnerType());
            }
            if (isset($values[$i])) {
                $this->assertInstanceOf($values[$i], $form->get($i)->get('value')->getConfig()->getType()->getInnerType());
            }
        }
    }

    private function assertFormOptions(array $options, $form)
    {
        for ($i = 0; $i < count($form); $i++) {
            foreach ($options[0] as $option => $optionValue) {
                $this->assertEquals($optionValue, $form->get($i)->get('key')->getConfig()->getOption($option));
            }
            foreach ($options[1] as $option => $optionValue) {
                $this->assertEquals($optionValue, $form->get($i)->get('value')->getConfig()->getOption($option));
            }
        }
    }
}

class ConcreteExtension extends AbstractExtension
{
    protected function loadTypes()
    {
        return array(new KeyValueType(), new KeyValueRowType());
    }

    protected function loadTypeGuesser()
    {
    }
}
