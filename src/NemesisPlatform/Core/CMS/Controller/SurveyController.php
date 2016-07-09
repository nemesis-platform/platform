<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.05.2015
 * Time: 14:14
 */

namespace NemesisPlatform\Core\CMS\Controller;

use NemesisPlatform\Core\CMS\Entity\SiteSurvey;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class SurveyController
 *
 * @package NemesisPlatform\SurveyBundle\Controller\User
 * @Route("/survey")
 */
class SurveyController extends Controller
{
    /**
     * @param \NemesisPlatform\Core\CMS\Entity\SiteSurvey $survey
     * @param Request                                          $request
     *
     * @Route("/take/{alias}", name="user_survey_take_by_alias")
     * @Route("/take/i/{id}", name="user_survey_take_by_id")
     *
     * @Template()
     *
     * @return Response
     */
    public function takeAction(SiteSurvey $survey, Request $request)
    {
        $user = $this->getUser();

        if ($survey->isLocked() && !$this->isGranted('ROLE_SURVEY_PREVIEW')) {
            throw new AccessDeniedHttpException('Survey is closed. Answers are not accepted.');
        }

        if (!($survey->isPublic() || ($user instanceof UserInterface))) {
            throw new AccessDeniedHttpException('This survey is available only for registered users');
        }

        $form = $this->createForm('survey', null, ['survey' => $survey]);
        $form->add(
            'submit',
            'submit',
            [
                'label' => 'Отправить',
                'attr'  => ['class' => 'center-block'],
            ]
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($form->getData());
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Результаты опроса сохранены');

            return $this->redirectToRoute('user_survey_take_by_alias', ['alias' => $survey->getAlias()]);
        } elseif ($form->isSubmitted()) {
            $this->get('session')->getFlashBag()->add(
                'danger',
                'Результат опроса не сохранен. Проверьте форму на наличие ошибок'
            );
        }

        return ['form' => $form->createView(), 'survey' => $survey];
    }
}
