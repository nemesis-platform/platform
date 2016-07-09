<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 28.05.2014
 * Time: 14:30
 */

namespace NemesisPlatform\Core\Account\Controller;

use NemesisPlatform\Core\Account\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class ServiceController
 *
 * @package NemesisPlatform\Game\Controller
 *
 */
class ServiceController extends Controller
{


    /**
     * @Route("/service/password_forgot/{code}", name="site_service_password_restore")
     * @Template()
     * @param Request $request
     * @param         $code
     *
     * @return Response
     */
    public function passwordChangeAction(Request $request, $code)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(
            ['pwdCode' => $code]
        );

        if (!$user) {
            throw new BadRequestHttpException('Некорректный код восстановления');
        }

        $form = $this->createFormBuilder()
                     ->add(
                         'password',
                         'repeated',
                         [
                             'type' => 'password',
                             'first_options'  => ['label' => 'Пароль'],
                             'second_options' => ['label' => 'Повторите пароль'],
                         ]
                     )
                     ->add('submit', 'submit', ['label' => 'Изменить'])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            $user->setPassword($encoder->encodePassword($form->get('password')->getData(), $user->getSalt()));

            $this->getDoctrine()->getManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Пароль успешно изменен');

            return $this->redirect($this->generateUrl('login'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/service/password_forgot", name="site_service_password_restore_request")
     * @Template()
     * @param Request $request
     *
     * @return Response
     */
    public function requestPasswordChangeAction(Request $request)
    {
        $form = $this->createFormBuilder()
                     ->add('email', 'email', ['label' => 'Логин'])
                     ->add('submit', 'submit', ['label' => 'Запросить одноразовый код'])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $email = $form->get('email')->getData();
            $user  = $this->getDoctrine()->getRepository(User::class)->findOneBy(
                ['email' => $email]
            );

            if ($user) {
                $code = sha1(uniqid('code_', true));
                $user->setPwdCode($code);

                $this->getDoctrine()->getManager()->flush();


                $message = Swift_Message::newInstance()
                                        ->setSubject(
                                            'Восстановление пароля - '.$this->get('site.manager')->getSite()->getName()
                                        )
                                        ->setFrom(
                                            $this->get('site.manager')->getSite()->getEmail(),
                                            $this->get('site.manager')->getSite()->getName()
                                        )
                                        ->setTo($user->getEmail())
                                        ->setBody(
                                            $this->renderView(
                                                'CoreBundle:MailTemplates:passwordRecovery.html.twig',
                                                [
                                                    'user' => $user,
                                                    'url'  => $this->generateUrl(
                                                        'site_service_password_restore',
                                                        ['code' => $user->getPwdCode()],
                                                        UrlGeneratorInterface::ABSOLUTE_URL
                                                    ),
                                                ]
                                            ),
                                            'text/html'
                                        )
                                        ->addPart(
                                            $this->renderView(
                                                'CoreBundle:MailTemplates:passwordRecovery.txt.twig',
                                                [
                                                    'user' => $user,
                                                    'url'  => $this->generateUrl(
                                                        'site_service_password_restore',
                                                        ['code' => $user->getPwdCode()],
                                                        UrlGeneratorInterface::ABSOLUTE_URL
                                                    ),
                                                ]
                                            ),
                                            'text/plain'
                                        );

                $this->get('mailer')->send($message);
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                'На указанный адрес выслано письмо, содержащее инструкции по сбросу пароля'
            );

            return $this->redirect($this->generateUrl('login'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param $code
     *
     * @return RedirectResponse
     * @Route("/register/confirm/{code}", name="site_service_check_email")
     *
     */
    public function confirmEmailAction($code)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(
            ['code' => $code]
        );
        if (!$user) {
            throw new BadRequestHttpException('Недействительный код');
        }

        $user->setStatus(User::EMAIL_CONFIRMED);
        $user->setCode(null);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('site_service_login_activated'));
    }


    /**
     * @param Request $request
     *
     * @return Response
     * @Route("/login",name="login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                Security::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        return
            [
                // last username entered by the user
                'last_username' => $session->get(Security::LAST_USERNAME),
                'error'         => $error,

            ];
    }
}
