<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 03.07.2015
 * Time: 13:56
 */

namespace NemesisPlatform\Admin\Form\Type\Block;

use NemesisPlatform\Core\CMS\Entity\Block\AbstractBlock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AbstractBlockType extends AbstractType
{

    /** @var  string */
    private $classname;

    /**
     * AbstractBlockType constructor.
     *
     * @param string $classname
     */
    public function __construct($classname = AbstractBlock::class)
    {
        $this->classname = $classname;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'abstract_block';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('description');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => $this->classname,
            ]
        );
    }
}
