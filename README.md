KeyValueFormBundle
==================

A form type for managing key-value pairs

To add to your form, use the alias `burgov_key_value`:

```php
$builder->add('parameters', 'burgov_key_value', array('value_type' => 'text'));

// or

$formFactory->create('burgov_key_value', $data, array('value_type' => 'text'));
```

The type extends the collection type, so for rendering it in the browser, the same logic is used. See the 
[Symfony docs on collection types](http://symfony.com/doc/current/cookbook/form/form_collections.html) for
an example of how to render it client side.

The type adds three options to the collection type options, of which one is required:

`value_type` (required) defines which form type to use to render the value field
`value_options` optional options to the child defined in `value_type`
`allowed_keys` if this option is provided, the key field (which is usually a simple text field) will change
to a `choice` field, and allow only those values you supplied in the this option.
