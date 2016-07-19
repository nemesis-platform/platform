<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.07.2014
 * Time: 12:23
 */

namespace NemesisPlatform\Core\Account\Controller;

use DateTime;
use NemesisPlatform\Core\Account\Entity\Phone;
use NemesisPlatform\Core\Account\Form\Type\UserAdditionalDataType;
use NemesisPlatform\Game\Entity\Participant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class PreferencesController
 *
 * @package NemesisPlatform\Game\Controller\Account
 * @Route("/account/preferences")
 */
class PreferencesController extends Controller
{

    /**
     * @Template()
     * @Route("/seasons", name="site_user_seasons_list")
     * @Method("GET")
     * @return Response
     */
    public function viewSeasonsAction()
    {
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->getUser();

        return ['seasons' => $user->getParticipations()];
    }

    /**
     * @param Request     $request
     * @Template()
     * @Route("/{participant}/edit", name="site_user_seasons_edit")
     * @Method({"GET","POST"})
     * @param Participant $participant
     *
     * @return Response
     */
    public function editParticipationAction(Request $request, Participant $participant)
    {
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->getUser();

        $manager = $this->getDoctrine()->getManager();

        if ($participant->getUser() !== $user) {
            throw new AccessDeniedHttpException('Невозможно редактировать чужой профиль');
        }

        if (!$participant->getSeason()->isActive()) {
            throw new AccessDeniedHttpException('Сезон не активен');
        }

        $form = $this
            ->createForm(
                'participant',
                $participant,
                [
                    'season' => $participant->getSeason(),
                    'attr'   => ['style' => 'horizontal'],
                ]
            )
            ->add('submit', 'submit', ['label' => 'Обновить']);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Данные обновлены');

            return $this->redirect(
                $this->generateUrl(
                    'site_user_seasons_edit',
                    ['participant' => $participant->getId()]
                )
            );
        }

        return ['s_data' => $participant, 'form' => $form->createView()];
    }

    /**
     * @Template()
     * @Route("/", name="site_user_preferences")
     * @Method("GET")
     */
    public function showOverviewAction()
    {
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $this->getUser();

        return [];
    }

    /**
     * @Template()
     * @Route("/password_change", name="site_service_change_password")
     * @Method({"GET","POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function changePasswordAction(Request $request)
    {
        $form = $this->createFormBuilder()
                     ->add('old_password', 'password', ['label' => 'Старый пароль'])
                     ->add(
                         'password',
                         'repeated',
                         [
                             'type'           => "password",
                             'first_options'  => ['label' => 'Новый пароль'],
                             'second_options' => ['label' => 'Повторите'],
                         ]
                     )
                     ->add('submit', 'submit', ['label' => 'Изменить пароль'])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var \NemesisPlatform\Core\Account\Entity\User $user */
            $user    = $this->getUser();
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            if ($encoder->isPasswordValid(
                $user->getPassword(),
                $form->get('old_password')->getData(),
                $user->getSalt()
            )
            ) {
                $user->setPassword($encoder->encodePassword($form->get('password')->getData(), $user->getSalt()));
                $this->getDoctrine()->getManager()->flush();
                $this->get('session')->getFlashBag()->add('success', 'Пароль успешно изменен');

                return $this->redirect(
                    $this->generateUrl('site_user_preferences')
                );
            } else {
                $this->get('session')->getFlashBag()->add('danger', 'Некорректный старый пароль');
            }
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/avatar", name="site_preferences_avatar")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     *
     * @return Response
     */
    public function uploadAvatarAction(Request $request)
    {
        $form = $this->createFormBuilder()
                     ->add('avatar', 'file', ['label' => 'Изображение пользователя'])
                     ->add('submit', 'submit', ['label' => 'Прикрепить'])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var UploadedFile $file */
            $file                        = $form->get('avatar')->getData();
            $avatarConstraint            = new Image();
            $avatarConstraint->maxSize   = 2 * 1024 * 1024 * 8;
            $avatarConstraint->maxHeight = 640;
            $avatarConstraint->maxWidth  = 640;
            $avatarConstraint->minRatio  = 0.2;
            $avatarConstraint->maxRatio  = 5;


            $avaName = sha1(uniqid()).'.'.$file->guessExtension();
            /** @var ConstraintViolationListInterface $errors */
            $errors = $this->get('validator')->validate($file, [$avatarConstraint]);

            if (count($errors) === 0) {
                $file->move(
                    $this->get('service_container')->getParameter('kernel.root_dir').'/../web/uploads/avatars',
                    $avaName
                );

                /** @var \NemesisPlatform\Core\Account\Entity\User $user */
                $user = $this->getUser();

                $user->setPhoto($avaName);
                $this->getDoctrine()->getManager()->flush();

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Пользовательское изображение успешно прикреплено'
                );

                return $this->redirect(
                    $this->generateUrl('site_user_preferences')
                );
            } else {
                foreach ($errors as $value) {
                    /** @var ConstraintViolationInterface $value */
                    $this->get('session')->getFlashBag()->add('danger', $value->getMessage());
                }
            }
        }

        return ['form' => $form->createView()];
    }


    /**
     * @param Request $request
     * @Route("/edit_info", name="site_preferences_edit_info")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @return Response
     */
    public function manageInfoAction(Request $request)
    {
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(new UserAdditionalDataType(), $user);
        $form->add('submit', 'submit', ['label' => 'Сохранить']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('doctrine.orm.entity_manager')->flush();

            $this->get('session')->getFlashBag()->add('success', 'Данные обновлены');

            return $this->redirect($this->generateUrl('site_preferences_edit_info'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request $request
     * @Route("/manage_phones", name="site_preferences_manage_phones")
     * @Method({"GET","POST"})
     *
     * @return Response
     * @Template()
     */
    public function managePhonesAction(Request $request)
    {
        $manager = $this->getDoctrine()->getManager();
        $form    = $this->createFormBuilder(new Phone())
                        ->add(
                            'phonenumber',
                            'text',
                            [
                                'label'   => 'Номер телефона +7',
                                'pattern' => '\d{10}',
                                'attr'    => [
                                    'pattern'   => '\d{10}',
                                    'min'       => 9000000000,
                                    'max'       => 9999999999,
                                    'help_text' => 'Номер в формате 9261234567',
                                ],
                            ]
                        )
                        ->add('submit', 'submit', ['label' => 'Добавить телефон'])
                        ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var \NemesisPlatform\Core\Account\Entity\Phone $phone */
            $phone = $form->getData();
            $phone->setUser($this->getUser());
            $phone->setStatus(Phone::STATUS_UNCONFIRMED);
            $phone->setFirstConfirmed(new DateTime());


            $manager->persist($phone);
            $manager->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Телефон добавлен. Подтвердите его или удалите из списка'
            );

            return $this->redirect($this->generateUrl('site_preferences_manage_phones'));
        }

        return ['form' => $form->createView()];
    }
}
