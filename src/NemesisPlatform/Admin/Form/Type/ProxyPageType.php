<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 18.12.2014
 * Time: 14:22
 */

namespace NemesisPlatform\Admin\Form\Type;

use NemesisPlatform\Core\CMS\Entity\ProxyPage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

class ProxyPageType extends AbstractType
{
    /** @var  RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'nemesis_proxy_page_type';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alias', 'text');
        $builder->add(
            'route',
            'choice',
            [
                'choices' =>
                    array_combine(
                        array_keys($this->router->getRouteCollection()->all()),
                        array_keys($this->router->getRouteCollection()->all())
                    ),
            ]
        );
        $builder->add(
            'data',
            'key_value_collection',
            [
                'label'              => 'Параметры',
                'allow_add'          => true,
                'allow_delete'       => true,
                'add_button_text'    => 'Добавить параметр',
                'delete_button_text' => 'Удалить',
                'options'            => ['attr' => ['style' => 'inline']],
            ]
        );
        $builder->add('site', 'current_site', ['label' => 'Сайт']);

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $form      = $event->getForm();
                $routeName = $form->get('route')->getData();
                if (($rawRoute = $this->router->getRouteCollection()->get($routeName)) !== null) {
                    $route = $rawRoute->compile();
                    $variables = $route->getVariables();
                    $data = $form->get('data')->getData();

                    $cleanData = [];

                    $form->remove('data');
                    $form->add('data', 'form', ['compound' => true, 'attr' => ['style' => 'horizontal']]);
                    foreach ($variables as $variable) {
                        $form->get('data')->add($variable);
                        $cleanData[$variable] = array_key_exists($variable, $data) ? $data[$variable] : null;
                    }

                    $form->get('data')->setData($cleanData);
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ProxyPage::class,
                'attr'       => ['style' => 'horizontal'],
            ]
        );
    }
}
