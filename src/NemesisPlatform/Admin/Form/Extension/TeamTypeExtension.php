<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 17.12.2014
 * Time: 19:05
 */

namespace NemesisPlatform\Admin\Form\Extension;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Core\Account\Entity\Tag;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Season;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TeamTypeExtension extends AbstractTypeExtension
{
    /**
     * @var RouterInterface
     */
    protected $router;
    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param RouterInterface               $router
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, RouterInterface $router)
    {
        $this->authorizationChecker = $authorizationChecker;

        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder->add(
                'season',
                'entity',
                [
                    'label' => 'Сезонъ',
                    'class' => Season::class,
                    'group_by'      => 'site',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('s')
                                          ->addOrderBy('s.site', 'ASC')
                                          ->addOrderBy('s.name', 'DESC');
                    },
                ]
            );
            $builder->add(
                'tags',
                'collection',
                [
                    'type'               => 'entity',
                    'label'              => 'Метки',
                    'allow_add'          => 'true',
                    'allow_delete'       => 'true',
                    'options'            => [
                        'class'    => Tag::class,
                        'required' => true,
                    ],
                ]
            );
            $builder->add(
                'persistent_tag',
                'text',
                ['label' => 'Draft ID', 'required' => false]
            );
            $builder->add('admin_comment', 'text', ['label' => 'Комментарий', 'required' => false]);
            $builder->add(
                'frozen',
                'checkbox',
                [
                    'required' => false,
                    'label'    => 'Заморожено',
                    'attr'     => ['align_with_widget' => true],
                ]
            );
            $builder->add(
                'date',
                'datetime_local',
                ['required' => false, 'label' => 'Создано', 'widget' => 'single_text']
            );
            $builder->add(
                'form_date',
                'datetime_local',
                ['required' => false, 'label' => 'Сформировано', 'widget' => 'single_text']
            );

            $router = $this->router;
            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($router) {
                    /** @var \NemesisPlatform\Game\Entity\Team $team */
                    $team = $event->getData();
                    $form = $event->getForm();
                    if ($team && $team->getID()) {
                        $form->add(
                            'members',
                            'collection',
                            [
                                'type'               => 'entity_autocomplete',
                                'label'              => 'Состав',
                                'allow_add'          => 'true',
                                'allow_delete'       => 'true',
                                'options'            => [
                                    'label'                 => false,
                                    'class'                 => Participant::class,
                                    'action'                => $router->generate(
                                        'site_admin_participant_autocomplete',
                                        ['season' => $team->getSeason()->getId()],
                                        RouterInterface::RELATIVE_PATH
                                    ),
                                    'visible_property_path' => 'toString',
                                ],
                            ]
                        );
                    }
                }
            );
        }
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return 'team_type';
    }
}
