<?php

namespace Burgov\Bundle\KeyValueFormBundle;

class KeyValueContainer implements \ArrayAccess
{
    private $data;

    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
 