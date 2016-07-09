<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.05.2015
 * Time: 15:45
 */

namespace NemesisPlatform\Admin\Form\Extension;

use NemesisPlatform\Core\CMS\Entity\SiteSurvey;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SiteSurveyExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'survey_form';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('site', 'current_site');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => SiteSurvey::class]);
    }
}
