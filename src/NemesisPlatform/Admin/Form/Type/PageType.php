<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 30.05.2014
 * Time: 11:45
 */

namespace NemesisPlatform\Admin\Form\Type;

use NemesisPlatform\Components\Skins\Service\LayoutStorageInterface;
use NemesisPlatform\Components\Skins\Service\SkinRegistryInterface;
use NemesisPlatform\Core\CMS\Entity\NemesisSite;
use NemesisPlatform\Core\CMS\Entity\Page;
use NemesisPlatform\Core\CMS\Entity\PageRevision;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PageType extends AbstractType
{
    /** @var  SkinRegistryInterface */
    private $SkinRegistryInterface;
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * PageType constructor.
     *
     * @param SkinRegistryInterface $SkinRegistryInterface
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(SkinRegistryInterface $SkinRegistryInterface, TokenStorageInterface $tokenStorage)
    {
        $this->SkinRegistryInterface = $SkinRegistryInterface;
        $this->tokenStorage          = $tokenStorage;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', null, ['label' => 'Заголовок']);
        $builder->add('alias', null, ['label' => 'Путь']);
        $builder->add(
            'lastRevision',
            new PageRevisionType(),
            [
                'empty_data'   => new PageRevision(),
                'by_reference' => false,
            ]
        );
        $builder->add('site', 'current_site');

        $builder->add(
            'language',
            'choice',
            ['label' => 'Язык', 'choices' => ['en' => 'Английский', 'ru' => 'Русский']]
        );
        $builder->add('created_time', 'datetime', ['widget' => 'single_text', 'label' => 'Создана']);


        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                /** @var \NemesisPlatform\Core\CMS\Entity\Page $page */
                $page = $event->getData();

                if ($page && $page->getSite()) {
                    $site = $page->getSite();

                    if ($site instanceof NemesisSite) {
                        /** @var LayoutStorageInterface|null $theme */
                        $theme = $this->SkinRegistryInterface->get(
                            $site->getThemeInstance() ? $site->getThemeInstance()->getTheme() : $site->getTheme()
                        );

                        $event->getForm()->add(
                            'template',
                            'choice',
                            [
                                'choices' => $theme ? $theme->all() : ['base' => 'base'],
                                'label'   => 'Шаблон',
                            ]
                        );
                    }
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var Page $page */
                $page = $event->getData();

                if ($page) {
                    $revision = $page->getLastRevision();
                    $revision->setCreatedTime(new \DateTime());
                    $revision->setPage($page);
                    $revision->setAuthor($this->getUser());
                    $page->getRevisions()->add($revision);
                }
            }
        );
    }

    private function getUser()
    {
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user instanceof UserInterface) {
            throw new AccessDeniedHttpException('Page editing is allowed only for registered users');
        }

        return $user;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => Page::class]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'page_type';
    }
}
