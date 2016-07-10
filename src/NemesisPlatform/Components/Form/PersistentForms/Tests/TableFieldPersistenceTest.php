<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.06.2015
 * Time: 15:43
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Tests;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\ChoiceField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\StringField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\TableField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\AbstractValue;

class TableFieldPersistenceTest extends AbstractKernelTest
{
    public function testSubFieldsDuplicateHandled()
    {
        $string = new StringField();
        $string->setName('string_type_'.sha1(__METHOD__));
        $string->setTitle('String field');
        self::$manager->persist($string);

        $table = new TableField();
        $table->setName('table_type_'.sha1(__METHOD__));
        $table->addField($string);
        $table->addField($string);
        self::$manager->persist($table);

        self::$manager->flush();
    }

    public function testSubFieldsWithEqualNamesHandled()
    {
        $string1 = new StringField();
        $string1->setName('string_type_'.sha1(__METHOD__));
        $string1->setTitle('String field 1');
        self::$manager->persist($string1);

        $string2 = new StringField();
        $string2->setName('string_type_'.sha1(__METHOD__));
        $string2->setTitle('String field 2');
        self::$manager->persist($string2);

        $table = new TableField();
        $table->setName('table_type_'.sha1(__METHOD__));
        $table->addField($string1);
        $table->addField($string2);
        self::$manager->persist($table);

        self::$manager->flush();
    }

    public function testTablePersistence()
    {
        $manager = self::$manager;

        $string = new StringField();
        $string->setName('string_type_'.sha1(__METHOD__));
        $string->setTitle('String field');
        $manager->persist($string);


        $choice = new ChoiceField();
        $choice->setName('choice_type_'.sha1(__METHOD__));
        $choice->setChoices(['v1' => 'choice1', 'v2' => 'choice2']);
        $choice->setTitle('Choice field');
        $manager->persist($choice);

        $table = new TableField();
        $table->setName('table_type_'.sha1(__METHOD__));
        $table->addField($string);
        $table->addField($choice);
        $manager->persist($table);

        $manager->flush();
        $manager->clear();

        /** @var TableField $fields */
        $field = $manager
            ->getRepository(AbstractField::class)->findOneBy(['name' => 'table_type_'.sha1(__METHOD__)]);
        self::assertNotNull($field);

        $builder = $this->getContainer()->get('form.factory')->createBuilder('form');

        $field->buildForm($builder);

        $form = $builder->getForm();

        $data = [
            'table_type_'.sha1(__METHOD__) => [
                [
                    'choice_type_'.sha1(__METHOD__) => 'v1',
                    'string_type_'.sha1(__METHOD__) => 'First string',
                ],
                [
                    'choice_type_'.sha1(__METHOD__) => 'v2',
                    'string_type_'.sha1(__METHOD__) => 'Second string',
                ],
                [
                    'choice_type_'.sha1(__METHOD__) => 'v1',
                    'string_type_'.sha1(__METHOD__) => 'Third string',
                ],
            ],
        ];

        $form->submit($data);
        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid(), (string)$form->getErrors(true, true));

        $values = $form->getData();

        self::assertCount(1, $values);

        foreach ($values as $value) {
            $manager->persist($value);
        }

        $manager->flush();
        $manager->clear();

        $stored = $manager->getRepository(AbstractValue::class)->findBy(['field' => $field]);
        self::assertCount(1, $stored);
        self::assertCount(3, $stored[0]->getValue());

        foreach ($stored[0]->getValue() as $row) {
            self::assertInternalType('array', $row);
            self::assertCount(2, $row);
        }

        $values = [];
        foreach ($stored as $value) {
            $values[$value->getField()->getName()] = $value;
        }

        $builder = $this->getContainer()->get('form.factory')->createBuilder('form');
        $field->buildForm($builder);
        $form = $builder->getForm();

        $form->setData($values);
        self::assertTrue($form->isSynchronized());

        $form->submit($data);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid());
    }
}
