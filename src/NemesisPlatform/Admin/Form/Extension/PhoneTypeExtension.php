<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 17.12.2014
 * Time: 18:22
 */

namespace NemesisPlatform\Admin\Form\Extension;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Core\Account\Entity\Phone;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PhoneTypeExtension extends AbstractTypeExtension
{

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;
    /** @var  EntityManagerInterface */
    private $objectManager;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EntityManagerInterface $objectManager
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->objectManager        = $objectManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder->remove('phonenumber');
            $builder->add(
                'phonenumber',
                'text',
                [
                    'label' => 'Номер телефона',
                ]
            );

            $builder->add(
                'status',
                'choice',
                [

                    'choices'    => [
                        Phone::STATUS_ACTIVE      => 'Активный',
                        Phone::STATUS_ARCHIVED    => 'Архивный',
                        Phone::STATUS_PENDING     => 'Выслан код',
                        Phone::STATUS_UNCONFIRMED => 'Не подтвержден',
                    ],
                    'empty_data' => Phone::STATUS_ACTIVE,
                    'label'      => 'Статус',
                ]
            );
            $builder->add(
                'first_confirmed',
                'datetime_local',
                ['empty_data' => new DateTime(), 'label' => 'Подтвержден']
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
        return 'phone_type';
    }
}
