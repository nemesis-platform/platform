<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-11
 * Time: 16:14
 */

namespace NemesisPlatform\Core\Account\Controller;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Core\Account\Entity\ConfirmationSMS;
use NemesisPlatform\Core\Account\Entity\Phone;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class MobileController
 * @Route("/account/mobile")
 *
 * @package NemesisPlatform\Game\Controller
 */
class MobileController extends Controller
{
    /**
     * @Route("/{id}/send", name="mobile_code_send")
     * @param Request $request
     * @param         $id
     *
     * @return Response
     */
    public function sendCodeAction(Request $request, $id)
    {
        $manager = $this->getDoctrine()->getManager();
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        /** @var Phone $phone */
        $phone = $manager->getRepository(Phone::class)->find($id);
        if (!$phone) {
            throw new BadRequestHttpException('Некорректный телефон');
        }

        if ($phone->getUser() !== $user) {
            throw new AccessDeniedException('Невозможно подтвердить чужой номер телефона');
        }

        if ($phone->isNotConfirmed()) {

            $sms = $this->get('sms_delivery.sender');

            $phone->setCode(Phone::generateCode());

            $message = new ConfirmationSMS($phone->getPhonenumber(), $phone->getCode());
            if ($sms->spoolMessage($message) === true) {
                $phone->setStatus(Phone::STATUS_PENDING);
                $this->get('session')->getFlashBag()->add(
                    'info',
                    'На указанный номер телефона выслано сообщение, содержащее проверочный код'
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    'Ошибка отправки sms. Проверьте номер или попробуйте еще раз'
                );
            }

            $manager->flush();
        } else {
            $this->get('session')->getFlashBag()->add(
                'danger',
                'Некорректный статус телефона для проверки'
            );
        }

        return $this->redirect(
            $request->headers->get('referer', $this->generateUrl('site_account_show'))
        );
    }

    /**
     * @param Request $request
     * @Route("/{id}/cancel", name="mobile_code_remove")
     * @param         $id
     *
     * @return RedirectResponse
     */
    public function removeCodeAction(Request $request, $id)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        /** @var \NemesisPlatform\Core\Account\Entity\Phone $phone */
        $phone = $manager->getRepository(Phone::class)->find($id);
        if (!$phone) {
            throw new BadRequestHttpException('Некорректный телефон');
        }

        if ($phone->getUser() !== $user) {
            throw new AccessDeniedException('Невозможно подтвердить чужой номер телефона');
        }

        if ($phone->isPendingConfirmation()) {
            $phone->setCode(null);
            $phone->setStatus(Phone::STATUS_UNCONFIRMED);
        }

        $manager->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @param Request $request
     * @Route("/{id}/remove", name="mobile_phone_remove")
     * @param         $id
     *
     * @return RedirectResponse
     */
    public function removePhoneAction(Request $request, $id)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        /** @var \NemesisPlatform\Core\Account\Entity\Phone $phone */
        $phone = $manager->getRepository(Phone::class)->find($id);
        if (!$phone) {
            throw new BadRequestHttpException('Некорректный телефон');
        }

        if ($phone->getUser() !== $user) {
            throw new AccessDeniedException('Невозможно подтвердить чужой номер телефона');
        }

        if ($phone->isNotConfirmed()) {
            $manager->remove($phone);
        }

        $manager->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/{id}/check", name="mobile_code_check")
     * @Template()
     * @param Request $request
     * @param         $id
     *
     * @return RedirectResponse|array
     * @throws AccessDeniedHttpException
     * @throws BadRequestHttpException
     */
    public function checkCodeAction(Request $request, Phone $id)
    {
        $phone = $id;
        if (!$phone || !$phone->isPendingConfirmation()) {
            throw new BadRequestHttpException('Некорректный телефон');
        }

        if ($phone->getUser() !== $this->getUser()) {
            throw new AccessDeniedHttpException('Невозможно подтвердить чужой номер телефона');
        }

        $form = $this->createFormBuilder()
            ->add('code', 'text')
            ->add('submit', 'submit', ['label' => 'Подтвердить'])
            ->setAction(
                $this->generateUrl(
                    'mobile_code_check',
                    ['id' => $phone->getID()]
                )
            )
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->validatePhone($phone, $form->get('code')->getData());

            } else {
                $this->get('session')->getFlashBag()->add('danger', 'Проверка кода неудачна. Попробуйте еще раз.');
            }

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Phone  $phone
     * @param string $code
     *
     * @return Phone
     */
    private function validatePhone(Phone $phone, $code)
    {
        $manager = $this->getDoctrine()->getManager();

        $phones = $manager->getRepository(Phone::class)->findBy(
            [
                'phonenumber' => $phone->getPhonenumber(),
                'status'      => [Phone::STATUS_ACTIVE, Phone::STATUS_ARCHIVED],
            ]
        );
        if ($phone->getCode() === $code) {
            if (count($phones) === 0) {
                $phone->setStatus(Phone::STATUS_ACTIVE);
                $phone->setCode(null);
                $phone->getUser()->setPhone($phone);

                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Проверка пройдена успешна. Телефон установлен в качестве активного.'
                );
            } else {
                $this->unconfirmPhone(
                    $phone,
                    'Невозможно подтвердить номер телефона, уже активированного другим участником.'
                );
            }
        } else {
            $this->unconfirmPhone(
                $phone,
                'Проверка кода неудачна. Проверьте телефон, запросите новый код или отмените проверку.'
            );
        }

        $manager->flush();

        return $phone;
    }

    /**
     * @param Phone  $phone
     * @param string $message
     */
    protected function unconfirmPhone(Phone $phone, $message)
    {
        $phone->setStatus(Phone::STATUS_UNCONFIRMED);
        $phone->setCode(null);
        $this->get('session')->getFlashBag()->add(
            'danger',
            $message
        );
    }

    /**
     * @param Request $request
     * @param         $id
     * @Route("/{id}/switch", name="mobile_phone_switch")
     *
     * @return Response
     */
    public function makeActiveAction(Request $request, Phone $id)
    {
        $manager = $this->getDoctrine()->getManager();
        /** @var \NemesisPlatform\Core\Account\Entity\User $user */
        $user = $this->get('security.token_storage')->getToken()->getUser();

        /** @var \NemesisPlatform\Core\Account\Entity\Phone $phone */
        $phone = $id;
        if ($phone->getUser() !== $user) {
            throw new AccessDeniedException('Невозможно подтвердить чужой номер телефона');
        }

        if (!$phone->isConfirmed()) {
            throw new AccessDeniedException('Невозможно переключиться на неподтвердженный номер');
        }

        $user->setPhone($phone);
        $manager->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
