<?php
/**
 * Created by PhpStorm.
 * User: scaytrase
 * Date: 19.12.2014
 * Time: 20:19
 */

namespace NemesisPlatform\Core\Account\Controller;

use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Core\Account\Form\Type\RegistrationFormType;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\SeasonedSite;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class UserController
 *
 * @package NemesisPlatform\Game\Controller
 */
class UserController extends Controller
{
    /**
     * @Route("/register", name="site_register")
     * @Template()
     * @param Request $request
     *
     * @return Response|array
     */
    public function registerAction(Request $request)
    {
        /** @var SeasonedSite $site */
        $site         = $this->get('site.manager')->getSite();
        $activeSeason = $site->getActiveSeason();

        if (!$activeSeason) {
            throw new AccessDeniedHttpException('В данный момент нет сезонов, доступных для регистрации');
        }

        if (!$this->get('security.authorization_checker')->isGranted('register')) {
            if ($this->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('site_account_show');
            } else {
                return [];
            }
        }

        $form = $this->createForm(new RegistrationFormType(), null, ['season' => $activeSeason]);

        $form->handleRequest($request);
        if (!$form->isValid()) {
            return ['form' => $form->createView()];

        }

        $manager = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $form->get('account')->getData();

        if ($manager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()])) {
            throw new LogicException('Пользователь уже зарегистрирован');
        }

        $user->setCode(sha1(uniqid('', true)));

        /** @var Participant $participant */
        $participant = $form->get('participant')->getData();

        $participant->setSeason($activeSeason);
        $participant->setUser($user);

        $manager->persist($user);
        $manager->persist($participant);


        $html = $this->renderView(
            'NemesisCoreBundle:MailTemplates:registrationConfirmation.html.twig',
            [
                'user' => $user,
                'url'  => $this->generateUrl(
                    'site_service_check_email',
                    ['code' => $user->getCode()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ]
        );

        $text = strip_tags($html);

        $message = $this->createRegistrationMessage($user, $html, $text);
        $this->get('mailer')->send($message, $failed);


        $manager->flush();

        return $this->redirect(
            $this->generateUrl('site_service_register_success')
        );
    }

    /**
     * @param $user
     * @param $html
     * @param $text
     *
     * @return mixed
     */
    protected function createRegistrationMessage($user, $html, $text)
    {
        return Swift_Message::newInstance()
            ->setSubject('Регистрация - '.$this->get('site.manager')->getSite()->getFullName())
            ->setFrom(
                $this->get('site.manager')->getSite()->getContactEmail(),
                $this->get('site.manager')->getSite()->getFullName()
            )
            ->setReplyTo($this->get('site.manager')->getSite()->getContactEmail())
            ->setTo($user->getEmail())
            ->setContentType('text/plain; charset=UTF-8')
            ->setBody(
                $html,
                'text/html'
            )
            ->addPart($text, 'text/plain');
    }

    /**
     * @param Request $request
     * @Template()
     *
     * @return Response
     * @Security("is_granted('update_essentials')")
     * @Route("/account/edit", name="site_account_edit")
     */
    public function editAction(Request $request)
    {
        $form
            = $this->createForm('user_type', $this->getUser())
            ->add('submit', 'submit', ['label' => 'Сохранить']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $this->addFlash('info', 'Анкета сохранена');
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('site_account_edit');
        }

        return ['form' => $form->createView()];
    }
}
