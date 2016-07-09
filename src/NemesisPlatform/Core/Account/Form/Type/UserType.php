<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-31
 * Time: 21:58
 */

namespace NemesisPlatform\Core\Account\Form\Type;

use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserType extends AbstractType
{

    /** @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface */
    private $encoderFactory;

    /**
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            'email',
            [
                'label' => 'Почта',
            ]
        );
        $builder->add(
            'password',
            'repeated',
            [
                'type'           => 'password',
                'mapped'         => false,
                'first_options'  => ['label' => 'Пароль', 'attr' => ['style' => 'horizontal']],
                'second_options' => ['label' => 'Повторите пароль', 'attr' => ['style' => 'horizontal']],
            ]
        );
        $builder->add('lastname', 'text', ['required' => true, 'label' => 'Фамилия']);
        $builder->add('firstname', 'text', ['required' => true, 'label' => 'Имя']);
        $builder->add('middlename', 'text', ['required' => true, 'label' => 'Отчество']);
        $builder->add(
            'birthDate',
            'birthday',
            [
                'widget'   => 'single_text',
                'required' => true,
                'label'    => 'Дата рождения',
                'format'   => 'dd.MM.yyyy',
                'attr'     => ['placeholder' => 'dd.mm.yyyy'],
            ]
        );
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                /** @var \NemesisPlatform\Core\Account\Entity\User $user */
                $user = $event->getData();
                if ($user && $user->getID() !== null) {
                    $form->remove('email');
                    $form->remove('password');
                }
            }
        );
        $factory = $this->encoderFactory;
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            /** Событие установки пароля после первичного сохранения анкеты */
            function (FormEvent $event) use ($factory) {
                /** @var \NemesisPlatform\Core\Account\Entity\User $user */
                $user = $event->getData();
                $form = $event->getForm();
                if ($form->has('password') && $form->get('password')->getData()) {
                    $encoder = $factory->getEncoder($user);
                    $user->setPassword($encoder->encodePassword($form->get('password')->getData(), $user->getSalt()));
                }
            }
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'empty_data' => function (FormInterface $form) {
                    return new User(
                        $form->get('email')->getData(),
                        bin2hex(random_bytes(20)),
                        $form->get('firstname')->getData(),
                        $form->get('lastname')->getData()
                    );
                },
                'data_class' => User::class,
                'attr'        => ['style' => 'horizontal'],
                'constraints' => [new UniqueEntity(['fields' => ['email']])],
            ]
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_type';
    }
}
