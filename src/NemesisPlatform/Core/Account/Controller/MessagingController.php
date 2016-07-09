<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 14.08.2014
 * Time: 16:38
 */

namespace NemesisPlatform\Core\Account\Controller;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Core\Account\Entity\PrivateMessage;
use NemesisPlatform\Core\Account\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MessagingController
 * @Route("/account")
 *
 * @package NemesisPlatform\Game\Controller
 */
class MessagingController extends Controller
{
    /**
     * @Route("/reply/{message}",name="messaging_reply")
     * @Template()
     * @param Request        $request
     * @param PrivateMessage $message
     *
     * @return Response
     */
    public function replyAction(Request $request, PrivateMessage $message)
    {
        $manager = $this->getDoctrine()->getManager();
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->getUser();

//        if (!$this->isGranted('ROLE_CONFIRMED_PHONE')) {
//            throw new AccessDeniedHttpException(
//                'Вы должны иметь подтвержденный номер телефона, чтобы иметь возможность писать личные сообщения'
//            );
//        }

        if (!($message->getRecipient() === $user || $message->getSender() === $user)) {
            throw new AccessDeniedHttpException();
        }

        $reply = new PrivateMessage();
        $reply->setParentMessage($message);

        $sender    = $message->getSender();
        $recipient = $message->getRecipient();

        $reply->setSender($sender === $user ? $sender : $recipient);
        $reply->setRecipient($sender === $user ? $recipient : $sender);

        $builder = $this
            ->createForm('private_message', $reply)
            ->add('submit', 'submit', ['label' => 'Отправить']);

        $builder->handleRequest($request);

        $message->setRead(true);

        if ($builder->isValid()) {
            $manager->persist($reply);
            $manager->flush();

            return $this->redirect(
                $this->generateUrl(
                    'messaging_send',
                    ['user' => $reply->getRecipient()->getId()]
                )
            );
        }

        /** @var EntityRepository $mrepo */
        $mrepo = $this->getDoctrine()->getRepository(PrivateMessage::class);

        $queryBuilder = $mrepo
            ->createQueryBuilder('m')
            ->select('m')
            ->orWhere('m.sender = :user AND m.recipient =:recipient')
            ->orWhere('m.recipient = :user AND m.sender = :recipient')
            ->setParameter('recipient', $recipient)
            ->setParameter('user', $sender);

        /** @var \NemesisPlatform\Core\Account\Entity\PrivateMessage[] $messages */
        $messages = $queryBuilder->getQuery()->getResult();

        foreach ($messages as $msg) {
            $msg->setRead(true);
        }

        return [
            'form'      => $builder->createView(),
            'recipient' => $reply->getRecipient(),
            'message'   => $message,
            'messages'  => $messages,
        ];
    }

    /**
     * @param Request $request
     * @Route("/pm/{user}",name="messaging_send")
     * @param User    $user
     *
     * @return RedirectResponse
     * @Template()
     */
    public function sendMessageAction(Request $request, User $user)
    {
        $manager = $this->getDoctrine()->getManager();

        if (!$user) {
            throw new NotFoundHttpException();
        }

//        if (!$this->isGranted('ROLE_CONFIRMED_PHONE')) {
//            throw new AccessDeniedHttpException(
//                'Вы должны иметь подтвержденный номер телефона, чтобы иметь возможность писать личные сообщения'
//            );
//        }


        $message = new PrivateMessage();
        $message->setRecipient($user);
        $message->setSender($this->getUser());
        $builder = $this
            ->createForm('private_message', $message)
            ->add('submit', 'submit', ['label' => 'Отправить']);

        $builder->handleRequest($request);

        if ($builder->isValid()) {
            $manager->persist($message);
            $manager->flush();

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        /** @var EntityRepository $mrepo */
        $mrepo = $this->getDoctrine()->getRepository(PrivateMessage::class);

        $queryBuilder = $mrepo
            ->createQueryBuilder('m')
            ->select('m')
            ->orWhere('m.sender = :user AND m.recipient =:recipient')
            ->orWhere('m.recipient = :user AND m.sender = :recipient')
            ->setParameter('recipient', $user)
            ->setParameter('user', $this->getUser());

        /** @var \NemesisPlatform\Core\Account\Entity\PrivateMessage[] $messages */
        $messages = $queryBuilder->getQuery()->getResult();

        foreach ($messages as $msg) {
            $msg->setRead(true);
        }

        return ['form' => $builder->createView(), 'recipient' => $user, 'messages' => $messages];
    }

    /**
     * @return array
     * @Template()
     * @Route("/pm",name="messaging_list")
     */
    public function listMessagesAction()
    {
        /** @var EntityRepository $mrepo */
        $mrepo = $this->getDoctrine()->getRepository(PrivateMessage::class);

        $queryBuilder = $mrepo
            ->createQueryBuilder('m')
            ->select('m')
            ->orWhere('m.sender = :user')
            ->orWhere('m.recipient = :user')
            ->setParameter('user', $this->getUser());

        /** @var \NemesisPlatform\Core\Account\Entity\PrivateMessage[] $messages */
        $messages = $queryBuilder->getQuery()->getResult();

        /** @var \NemesisPlatform\Core\Account\Entity\PrivateMessage[] $unm */
        $unm = [];

        foreach ($messages as $msg) {
            if ($msg->getRecipient() === $this->getUser()) {
                if (!$msg->isRead()) {
                    $unm[] = $msg;
                    $msg->setRead(true);
                }
            }
        }
        $this->getDoctrine()->getManager()->flush();
        foreach ($unm as $msg) {
            $msg->setRead(false);
        }

        return ['messages' => $messages];
    }
}
