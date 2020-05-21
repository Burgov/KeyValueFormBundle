KeyValueFormBundle
==================

A form type for managing key-value pairs.

Installation
------------

```bash
$ composer require burgov/key-value-form-bundle:@stable
```

Then add the bundle to your application:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Burgov\Bundle\KeyValueFormBundle\BurgovKeyValueFormBundle(),
        // ...
    );
}
```

Usage
-----

To add to your form, use the `KeyValueType` type:

```php
use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

$builder->add('parameters', KeyValueType::class, array('value_type' => TextType::class));

// or

$formFactory->create(KeyValueType::class, $data, array('value_type' => TextType::class));
```

The type extends the collection type, so for rendering it in the browser, the same logic is used. See the
[Symfony docs on collection types](http://symfony.com/doc/current/cookbook/form/form_collections.html) for
an example on how to render it client side.

The type adds four options to the collection type options, of which one is required:

  * `value_type` (required) defines which form type to use to render the value field
  * `value_options` optional options to the child defined in `value_type`
  * `key_type` defines which form type to use to render the key field (default is a `text` field)
  * `key_options` optional options to the child defined in `key_type`
  * `allowed_keys` if this option is provided, the key field (which is usually a simple text field) will change to a `choice` field, and allow only those values you supplied in the this option.
  * `use_container_object` see explanation below at 'The KeyValueCollection'

Besides that, this type overrides some defaults of the collection type and it's recommended you don't change them:
`type` is set to `BurgovKeyValueRow::class` and `allow_add` and `allow_delete` are always `true`.

Working with SonataAdminBundle
------------------------------

In order to render your form with add/remove buttons, you need to extend the template `SonataAdminBundle:Form:form_admin_fields.html.twig` and add this little piece of code
```twig
{% block burgov_key_value_widget %}
    {{- block('sonata_type_native_collection_widget') -}}
{% endblock %}
```

The KeyValueCollection
----------------------

To work with collections and the Symfony2 form layer, you can provide an adder
and a remover method. This however only works if the adder method expects one
argument only. This bundle provides composed key-value pairs.

Your model class typically will provide a method like:

```php
class Model
{
    public function addOption($key, $value)
    {
        $this->options[$key] = $value;
    }
}
```

This will lead to the add method not being found by the form layer. To work
around this problem, the bundle provides the KeyValueCollection object. To use
it, you need to set the `use_container_object` option on the form type to
`true`. A form definition could look like this:

```php
/** @var $builder Symfony\Component\Form\FormBuilderInterface */
$builder->add('options', 'burgov_key_value', array(
    'required' => false,
    'value_type' => TextType::class,
    'use_container_object' => true,
));
```

Your model class then needs to provide a `setOptions` method that accepts a
`Burgov\Bundle\KeyValueFormBundle\KeyValueContainer` argument. A flexible
implementation might look like this:

```php
class Model
{
    /**
     * Set the options.
     *
     * @param array|KeyValueContainer|\Traversable $data Something that can be converted to an array.
     */
    public function setOptions($options)
    {
        $this->options = $this->convertToArray($options);
    }

    /**
     * Extract an array out of $data or throw an exception if not possible.
     *
     * @param array|KeyValueContainer|\Traversable $data Something that can be converted to an array.
     *
     * @return array Native array representation of $data
     *
     * @throws InvalidArgumentException If $data can not be converted to an array.
     */
    private function convertToArray($data)
    {
        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof KeyValueContainer) {
            return $data->toArray();
        }

        if ($data instanceof \Traversable) {
            return iterator_to_array($data);
        }

        throw new InvalidArgumentException(sprintf('Expected array, Traversable or KeyValueContainer, got "%s"', is_object($data) ? get_class($data) : gettype($data)));
    }
}
```
