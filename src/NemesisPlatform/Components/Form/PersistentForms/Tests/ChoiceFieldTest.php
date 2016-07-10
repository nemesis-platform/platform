<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.06.2015
 * Time: 13:24
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Tests;


use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\Type\ChoiceField;
use Symfony\Component\Form\Test\FormBuilderInterface;
use Symfony\Component\Form\Test\TypeTestCase;

class ChoiceFieldTest extends TypeTestCase
{
    const FIELD_NAME = 'multiple_choice_type';

    public function testOptGroups()
    {
        $builder = $this->createTestFieldBuilder(
            [
                ['value' => 'choice3', 'optgroup' => 'other_group'],
                ['value' => 'choice1', 'optgroup' => 'test_group'],
                ['value' => 'choice2', 'optgroup' => 'test_group'],
            ]
        );

        self::assertTrue($builder->has(self::FIELD_NAME));
        $options = $builder->get(self::FIELD_NAME)->getOptions();
        self::assertArrayHasKey('choices', $options);
        self::assertEquals(
            [
                'other_group' => ['choice3' => 0],
                'test_group'  => ['choice1' => 1, 'choice2' => 2],
            ],
            $options['choices']
        );
    }

    /**
     * @param array $choices
     *
     * @return FormBuilderInterface
     */
    private function createTestFieldBuilder(array $choices = [])
    {
        $multipleChoice = new ChoiceField();
        $multipleChoice->setMultiple(true);
        $multipleChoice->setName(self::FIELD_NAME);
        $multipleChoice->setChoices($choices);
        $builder = $this->factory->createBuilder('form');
        $multipleChoice->buildForm($builder);

        return $builder;
    }

    public function testKeysAndInternalSorting()
    {
        $builder = $this->createTestFieldBuilder(
            [
                ['value' => 'choice1', 'optgroup' => 'test_group'],
                'best_choice' => ['value' => 'choice3', 'optgroup' => 'other_group'],
                ['value' => 'choice2', 'optgroup' => 'test_group'],
            ]
        );
        $options = $builder->get(self::FIELD_NAME)->getOptions();
        self::assertArrayHasKey('choices', $options);
        self::assertEquals(
            [
                'other_group' => ['choice3' => 'best_choice'],
                'test_group'  => ['choice1' => 0, 'choice2' => 1],
            ],
            $options['choices']
        );
    }

    public function testNoOptGroup()
    {
        $builder = $this->createTestFieldBuilder(
            [
                'best_choice' => ['value' => 'choice3', 'optgroup' => 'other_group'],
                ['value' => 'choice1', 'optgroup' => 'test_group'],
                ['value' => 'choice2'],
            ]
        );
        $options = $builder->get(self::FIELD_NAME)->getOptions();
        self::assertArrayHasKey('choices', $options);
        self::assertEquals(
            [
                'other_group' => ['choice3' => 'best_choice'],
                'test_group'  => ['choice1' => 0],
                'choice2'     => 1,
            ],
            $options['choices']
        );
    }
}
