<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.11.2014
 * Time: 12:41
 */

namespace NemesisPlatform\Core\Account\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

class RulesCheckbox extends AbstractType
{
    /** @var  RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'checkbox';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'translation_domain' => 'forms',
                    'required'           => true,
                    'mapped'             => false,
                    'label'              => 'rules_checkbox.label',
                    'attr'               => ['align_with_widget' => true],
                    'link'               => $this->router->generate(
                        'page_by_alias',
                        ['alias' => 'terms_and_rules']
                    ),
                ]
            )
            ->setRequired(['link']);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'rules_checkbox';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['link'] = $options['link'];
    }
}
