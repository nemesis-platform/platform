<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 01.06.2015
 * Time: 9:59
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Tests;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\ChoiceField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\NumberField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\StringField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\TextAreaField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;

class PersistenceTest extends AbstractKernelTest
{

    public function testStoring()
    {
        $manager = self::$manager;

        $string = new StringField();
        $string->setName('string_type');
        $manager->persist($string);

        $text = new TextAreaField();
        $text->setName('text_type');
        $manager->persist($text);

        $number = new NumberField();
        $number->setName('number_type');
        $manager->persist($number);

        $choice = new ChoiceField();
        $choice->setName('choice_type');
        $choice->setChoices(['choice1', 'choice2']);
        $manager->persist($choice);

        $multipleChoice = new ChoiceField();
        $multipleChoice->setMultiple(true);
        $multipleChoice->setName('multiple_choice_type');
        $multipleChoice->setChoices(['choice1', 'choice2', 'choice3']);
        $manager->persist($multipleChoice);

        $manager->flush();

        $manager->clear();

        /** @var AbstractField[] $fields */
        $fields = $manager->getRepository(AbstractField::class)->findAll();
        self::assertCount(5, $fields);

        $builder = $this->getContainer()->get('form.factory')->createBuilder('form');

        foreach ($fields as $field) {
            $field->buildForm($builder);
        }

        $form = $builder->getForm();
        $form->submit(
            [
                'string_type'          => 'the string to test',
                'text_type'            => 'Some text goes here',
                'number_type'          => 1,
                'choice_type'          => 0,
                //                'choice_type'          => 'choice1',
                'multiple_choice_type' => [0, 2],
                //                'multiple_choice_type' => ['choice1', 'choice3'],
            ]
        );

        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid(), (string)$form->getErrors(true, true));


        $values = $form->getData();

        self::assertCount(5, $values);

        foreach ($values as $value) {
            $manager->persist($value);
        }

        $manager->flush();
        $manager->clear();

        $stored = $manager->getRepository(AbstractValue::class)->findAll();

        self::assertCount(5, $stored);

        $form = $builder->getForm();

        $values = [];
        foreach ($stored as $value) {
            $values[$value->getField()->getName()] = $value;
        }
        $form->setData($values);

        self::assertTrue($form->isSynchronized());
        self::assertEquals(1.0, $form->get('number_type')->getData()->getValue());

        $form->createView();
    }
}
