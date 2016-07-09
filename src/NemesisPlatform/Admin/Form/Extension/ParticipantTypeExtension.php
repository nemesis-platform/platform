<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 17.12.2014
 * Time: 18:45
 */

namespace NemesisPlatform\Admin\Form\Extension;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Game\Entity\Season;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ParticipantTypeExtension extends AbstractTypeExtension
{

    /** @var  AuthorizationCheckerInterface */
    protected $security;

    /**
     * @param AuthorizationCheckerInterface $security
     */
    public function __construct(AuthorizationCheckerInterface $security)
    {
        $this->security = $security;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            $builder->add(
                'season',
                'entity',
                [
                    'class' => Season::class,
                    'label'         => 'Сезон',
                    'group_by'      => 'site',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository->createQueryBuilder('s')
                                          ->addOrderBy('s.site', 'ASC')
                                          ->addOrderBy('s.name', 'DESC');
                    },
                ]
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
        return 'season_data_type';
    }
}
