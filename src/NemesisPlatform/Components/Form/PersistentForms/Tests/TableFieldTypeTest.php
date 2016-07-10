<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 02.06.2015
 * Time: 13:45
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Tests;

use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\ChoiceField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\StringField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\TableField;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Value\Type\TableValue;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\TableRowType;
use NemesisPlatform\Components\Form\PersistentForms\Form\Type\TableType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\FormInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class TableFieldTypeTest extends TypeTestCase
{
    public function testTypeOperations()
    {
        $table = new TableField();
        $table->setName('table_field');

        $subStageChoice = new ChoiceField();
        $subStageChoice->setName('sub_choice_stage');
        $subStageChoice->setTitle('Stage');
        $subStageChoice->setMultiple(false);
        $subStageChoice->setExpanded(false);
        $subStageChoice->setChoices(['1' => 'first stage', '2' => 'second stage']);

        $subStageDescriptionString = new StringField();
        $subStageDescriptionString->setName('sub_string_description');
        $subStageDescriptionString->setTitle('Description');

        $subStageResultsString = new StringField();
        $subStageResultsString->setName('sub_string_results');
        $subStageResultsString->setTitle('Results');

        $table->addField($subStageChoice);
        $table->addField($subStageDescriptionString);
        $table->addField($subStageResultsString);

        $builder = $this->factory->createBuilder('form');

        $table->buildForm($builder);

        $data = [
            'table_field' => [
                0 => [
                    'sub_choice_stage'       => '1',
                    'sub_string_description' => 'First stage intro',
                    'sub_string_results'     => 'Gain reputation',
                ],
                1 => [
                    'sub_choice_stage'       => '1',
                    'sub_string_description' => 'First stage hard working',
                    'sub_string_results'     => 'Get significant results',
                ],
                2 => [
                    'sub_choice_stage'       => '2',
                    'sub_string_description' => 'Second stage distribution',
                    'sub_string_results'     => 'All failed',
                ],
            ],
        ];

        $expected = [
            0 => [
                'sub_choice_stage'       => 'first stage',
                'sub_string_description' => 'First stage intro',
                'sub_string_results'     => 'Gain reputation',
            ],
            1 => [
                'sub_choice_stage'       => 'first stage',
                'sub_string_description' => 'First stage hard working',
                'sub_string_results'     => 'Get significant results',
            ],
            2 => [
                'sub_choice_stage'       => 'second stage',
                'sub_string_description' => 'Second stage distribution',
                'sub_string_results'     => 'All failed',
            ],
        ];

        $form = $builder->getForm();

        $form->submit($data);

        self::assertTrue($form->isSynchronized());

        /** @var TableValue $tableValue */
        $tableValue = $form->get('table_field')->getData();

        self::assertInstanceOf(
            TableValue::class,
            $tableValue
        );

        self::assertTrue(is_array($tableValue->getValue()));

        $array = $tableValue->getValue();

        self::assertCount(3, $array);

        foreach ($array as $row) {
            self::assertTrue(is_array($row));
            self::assertCount(3, $row);
            self::assertArrayHasKey('sub_choice_stage', $row);
            self::assertArrayHasKey('sub_string_description', $row);
            self::assertArrayHasKey('sub_string_results', $row);
        }


        self::assertEquals($expected, $tableValue->getRenderValue());

        self::assertEquals(
            [
                'sub_choice_stage'       => 'Stage',
                'sub_string_description' => 'Description',
                'sub_string_results'     => 'Results',
            ],
            $tableValue->getHeaders()
        );

        $form = $builder->getForm();

        $form->setData(['table_field' => $tableValue]);

        self::assertTrue($form->isSynchronized());

        self::assertEquals($expected, $form->get('table_field')->getData()->getRenderValue());

        $form->submit($data);

        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid());
    }

    protected function getExtensions()
    {
        /** @var FormInterface[] $types */
        $types   = [];
        $types[] = new TableType($types);
        $types[] = new TableRowType();

        $extensions = [];

        foreach ($types as $type) {
            $extensions[$type->getName()] = $type;
        }

        return [new PreloadedExtension($extensions, [])];
    }
}
