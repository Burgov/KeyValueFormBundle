<?php

namespace Burgov\Bundle\KeyValueFormBundle\Tests\Form\Fixtures;

class ObjectWithAdderRemoverSetterAndGetter
{
    private $values = array();

    public function setValues(array $values)
    {
        $this->values = $values;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function addValue($key, $value)
    {
        $this->values[$key] = $value;
    }

    public function removeValue($key)
    {
        unset($this->values[$key]);
    }
}
 