<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-05-27
 * Time: 23:49
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Tests;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\ChoiceField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\NumberField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\StringField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\TextAreaField;
use Symfony\Component\Form\Test\TypeTestCase;

class StoredFormsTest extends TypeTestCase
{
    public function testTransformations()
    {
        $string = new StringField();
        $string->setName('string_type');

        $text = new TextAreaField();
        $text->setName('text_type');

        $number = new NumberField();
        $number->setName('number_type');

        $choice = new ChoiceField();
        $choice->setName('choice_type');
        $choice->setChoices(array('choice1', 'choice2'));

        $multipleChoice = new ChoiceField();
        $multipleChoice->setMultiple(true);
        $multipleChoice->setName('multiple_choice_type');
        $multipleChoice->setChoices(array('choice1', 'choice2', 'choice3'));


        /** @var AbstractField[] $fields */
        $fields = array($string, $text, $number, $choice, $multipleChoice);

        $builder = $this->factory->createBuilder('form');

        foreach ($fields as $field) {
            $field->buildForm($builder);
        }

        $data = array(
            'string_type'          => 'the string to test',
            'text_type'            => 'Some text goes here',
            'number_type'          => 1,
            'choice_type'          => 0,
            'multiple_choice_type' => array(0, 2)
        );

        $form = $builder->getForm();
        $form->submit($data);

        self::assertTrue($form->isSynchronized());

        self::assertInstanceOf(
            'NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\PlainValue',
            $form->get('string_type')->getData()
        );
        self::assertEquals('the string to test', $form->get('string_type')->getData()->getValue());
        self::assertEquals($string, $form->get('string_type')->getData()->getField());

        self::assertInstanceOf(
            'NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\TextValue',
            $form->get('text_type')->getData()
        );
        self::assertEquals('Some text goes here', $form->get('text_type')->getData()->getValue());
        self::assertEquals($text, $form->get('text_type')->getData()->getField());

        self::assertInstanceOf(
            'NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\PlainValue',
            $form->get('number_type')->getData()
        );
        self::assertEquals(1, $form->get('number_type')->getData()->getValue());
        self::assertTrue(1.0 === $form->get('number_type')->getData()->getValue());
        self::assertEquals($number, $form->get('number_type')->getData()->getField());

        self::assertInstanceOf(
            'NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\ChoiceValue',
            $form->get('choice_type')->getData()
        );

        self::assertEquals(0, $form->get('choice_type')->getData()->getValue());
        self::assertEquals('choice1', $form->get('choice_type')->getData()->getRenderValue());
        self::assertEquals($choice, $form->get('choice_type')->getData()->getField());

        self::assertInstanceOf(
            'NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\ChoiceValue',
            $form->get('multiple_choice_type')->getData()
        );

        self::assertEquals(array(0, 2), $form->get('multiple_choice_type')->getData()->getValue());
        self::assertEquals(array('choice1', 'choice3'), $form->get('multiple_choice_type')->getData()->getRenderValue());
        self::assertEquals($multipleChoice, $form->get('multiple_choice_type')->getData()->getField());
    }
}
