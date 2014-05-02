<?php

namespace Burgov\Bundle\KeyValueFormBundle\Tests\Form\Type;

use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueRowType;
use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType;
use Symfony\Component\Form\AbstractExtension;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\Test\TypeTestCase;

class KeyValueTypeTest extends TypeTestCase
{
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

        $builder = $this->factory->createBuilder('burgov_key_value', $originalData, array('value_type' => 'text'));

        $form = $builder->getForm();

        $this->assertFormTypes(array('text', 'text'), $form);

        $form->submit($submitData);
        $this->assertTrue($form->isValid(), $form->getErrorsAsString());

        $this->assertFormTypes(array('text', 'text', 'text'), $form);

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

        $builder = $this->factory->createBuilder('burgov_key_value', null, array('value_type' => 'choice', 'value_options' => array(
            'choice_list' => new ObjectChoiceList(array($obj1, $obj2), 'name', array(), null, 'id')
        )));

        $form = $builder->getForm();

        $this->assertFormTypes(array(), $form);

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

        $this->assertFormTypes(array('choice', 'choice'), $form);

        $this->assertTrue($form->isValid());

        $this->assertSame(array('key1' => $obj2, 'key2' => $obj1), $form->getData());
    }

    private function assertFormTypes(array $types, $form)
    {
        $this->assertCount(count($types), $form);
        foreach ($types as $key => $type) {
            $this->assertEquals($type, $form->get($key)->get('value')->getConfig()->getType()->getInnerType()->getName());
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