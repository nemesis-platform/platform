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
    public function testOptGroups()
    {
        $builder = $this->createTestFieldBuilder(
            array(
                array('value' => 'choice3', 'optgroup' => 'other_group'),
                array('value' => 'choice1', 'optgroup' => 'test_group'),
                array('value' => 'choice2', 'optgroup' => 'test_group'),
            )
        );

        self::assertTrue($builder->has('multiple_choice_type'));
        $options = $builder->get('multiple_choice_type')->getOptions();
        self::assertArrayHasKey('choices', $options);
        self::assertEquals(
            array(
                'other_group' => array(0 => 'choice3'),
                'test_group'  => array(1 => 'choice1', 2 => 'choice2'),
            ),
            $options['choices']
        );
    }

    /** @return FormBuilderInterface */
    private function createTestFieldBuilder(array $choices = array())
    {
        $multipleChoice = new ChoiceField();
        $multipleChoice->setMultiple(true);
        $multipleChoice->setName('multiple_choice_type');
        $multipleChoice->setChoices($choices);
        $builder = $this->factory->createBuilder('form');
        $multipleChoice->buildForm($builder);

        return $builder;
    }

    public function testKeysAndInternalSorting()
    {
        $builder = $this->createTestFieldBuilder(
            array(
                array('value' => 'choice1', 'optgroup' => 'test_group'),
                'best_choice' => array('value' => 'choice3', 'optgroup' => 'other_group'),
                array('value' => 'choice2', 'optgroup' => 'test_group'),
            )
        );
        $options = $builder->get('multiple_choice_type')->getOptions();
        self::assertArrayHasKey('choices', $options);
        self::assertEquals(
            array(
                'other_group' => array('best_choice' => 'choice3'),
                'test_group'  => array(0 => 'choice1', 1 => 'choice2'),
            ),
            $options['choices']
        );
    }

    public function testNoOptGroup()
    {
        $builder = $this->createTestFieldBuilder(
            array(
                'best_choice' => array('value' => 'choice3', 'optgroup' => 'other_group'),
                array('value' => 'choice1', 'optgroup' => 'test_group'),
                array('value' => 'choice2'),
            )
        );
        $options = $builder->get('multiple_choice_type')->getOptions();
        self::assertArrayHasKey('choices', $options);
        self::assertEquals(
            array(
                'other_group' => array('best_choice' => 'choice3'),
                'test_group'  => array(0 => 'choice1'),
                1             => 'choice2',
            ),
            $options['choices']
        );
    }
}
