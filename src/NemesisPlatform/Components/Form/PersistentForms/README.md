[![Build Status](https://travis-ci.org/scaytrase/symfony-storable-forms.svg?branch=feature%2Foverhaul)](https://travis-ci.org/scaytrase/symfony-storable-forms)

# Symfony2 Stored Forms Bundle

## Description

This bundle was highly inspired by Drupal 6 CCK module mechanics. It allows to dynamically create custom web forms with 
highly customizable inputs without any change in the source code. The changes are only needed if the set of available 
field types should be extended.

Here is the attempt to reinvent the wheel on top of the Symfony2 Form components stored with Doctrine2.

## Installation

The best way to install this bundle is to use composer

```bash
    composer require "scaytrase/symfony-stored-forms-bundle:~3.0"
```

And include the bundle into the list of application bundles
 
```php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
                \\ ....
                new NemesisPlatform\Components\PersistentForms\PersistentForms(),
    
```

## Usage

The simplest way of usage is shown in the test suite

```php

        $string = new StringField();
        $string->setName('string_type');

        $text = new TextAreaField();
        $text->setName('text_type');

        $number = new NumberField();
        $number->setName('number_type');

        $choice = new ChoiceField();
        $choice->setName('choice_type');
        $choice->setChoices(array('choice1', 'choice2'));

        /** @var AbstractField[] $fields */
        $fields = array($string, $text, $number, $choice);

        $builder = $this->factory->createBuilder('form');

        foreach ($fields as $field) {
            $field->buildForm($builder);
        }

```

Then just the populate form either with manual `FormInterface::submit($data)` call or via 
`FormInterface::handleRequest($request)`. Each field from the form will return the `AbstractValue` object (see fields 
classes for more information). Both fields and values are ready to be stored at the database. 
