<?php

namespace Burgov\Bundle\KeyValueFormBundle\Tests\Form\Type;

use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType;
use Symfony\Component\Form\Test\TypeTestCase;

class KeyValueTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $originalData = array(
            'old-key1' => 'old-string-value1',
            'old-key2' => 'old-string-value2',
            'old-key3' => 8,
            'old-key4' => false
        );

        $submitData = array(
            array(
                'key' => 'key1',
                'type' => 'string',
                'value' => 'string-value'
            ),
            array(
                'key' => 'key2',
                'type' => 'integer',
                'value' => '5'
            ),
            array(
                'key' => 'key3',
                'type' => 'boolean',
                'value' => '1'
            )
        );

        $expectedData = array(
            'key1' => 'string-value',
            'key2' => 5,
            'key3' => true,
        );

        $builder = $this->factory->createBuilder(new KeyValueType(), $originalData);

        $form = $builder->getForm();

        $this->assertFormTypes(array('text', 'text', 'integer', 'checkbox'), $form);

        $form->submit($submitData);
        $this->assertTrue($form->isValid(), $form->getErrorsAsString());

        $this->assertFormTypes(array('text', 'integer', 'checkbox'), $form);

        $this->assertSame($expectedData, $form->getData());
    }

    private function assertFormTypes(array $types, $form) {
        $this->assertCount(count($types), $form);
        foreach($types as $key => $type) {
            $this->assertEquals($type, $form->get($key)->get('value')->getConfig()->getType()->getInnerType()->getName());
        }
    }
}