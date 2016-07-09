<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 17.12.2014
 * Time: 19:15
 */

namespace NemesisPlatform\Admin\Form\Extension;

use NemesisPlatform\Core\Account\Entity\Phone;
use NemesisPlatform\Core\Account\Entity\Tag;
use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserTypeExtension extends AbstractTypeExtension
{
    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder->setRequired(false);

            $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $user = $event->getData();

                    if ($user && $user->getID()) {
                        $form->add(
                            'phone',
                            'entity',
                            [
                                'class' => Phone::class,
                                'choices'     => $user->getPhones()->filter(
                                    function (Phone $phone) {
                                        return $phone->isConfirmed();
                                    }
                                )->toArray(),
                                'label'       => 'Текущий телефон',
                                'required'    => false,
                                'empty_value' => 'Не выбран',
                            ]
                        );
                    }
                    $form->add(
                        'phones',
                        'collection',
                        [
                            'options'            => [
                                'attr' => ['style' => 'horizontal'],
                            ],
                            'label'              => 'Телефоны',
                            'type'               => 'phone_type',
                            'allow_add'          => 'true',
                            'allow_delete'       => 'true',
                        ]
                    );

                    /** @var \NemesisPlatform\Core\Account\Entity\User $user */
                    $form->add(
                        'status',
                        'choice',
                        [
                            'label'   => 'Статус',
                            'choices' => [
                                User::EMAIL_CONFIRMED => 'Активирован',
                                User::EMAIL_PENDING   => 'Не подтверждена почта',
                                User::BANNED          => 'Заблокирован',
                            ],
                        ]
                    );
                    $form->add('email', null, ['label' => 'Почта']);
                    $form->add('code', null, ['label' => 'Код подтверждения почты']);
                    $form->add('pwdCode', null, ['label' => 'Код сброса пароля']);
                    $form->add('admin_comment', 'text', ['label' => 'Комментарий', 'required' => false]);
                    $form->add(
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
        return 'user_type';
    }
}
