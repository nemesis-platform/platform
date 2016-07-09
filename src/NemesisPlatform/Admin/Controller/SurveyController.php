<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.05.2015
 * Time: 14:14
 */

namespace NemesisPlatform\Admin\Controller;

use NemesisPlatform\Core\CMS\Entity\SiteSurvey;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SurveyController
 *
 * @package NemesisPlatform\SurveyBundle\Controller\Admin
 * @Route("/components/survey")
 */
class SurveyController extends Controller
{
    /**
     * @Route("/list", name="admin_survey_list")
     * @Template()
     *
     * @return Response
     */
    public function listAction()
    {
        return [
            'surveys' => $this->getDoctrine()->getManager()->getRepository(SiteSurvey::class)->findBy(
                ['site' => $this->get('site.manager')->getSite()]
            ),
        ];
    }

    /**
     * @param Request $request
     *
     * @Route("/create", name="admin_survey_create")
     * @Template()
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm('survey_form');
        $form->add('submit', 'submit');

        $form->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $manager->persist($form->getData());
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Опрос успешно создан');

            return $this->redirectToRoute('admin_survey_list');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param \NemesisPlatform\Core\CMS\Entity\SiteSurvey $survey
     * @param Request                                          $request
     *
     * @Route("/{survey}/edit", name="admin_survey_edit")
     * @Template()
     *
     * @return Response
     */
    public function editAction(SiteSurvey $survey, Request $request)
    {
        $form = $this->createForm('survey_form', $survey);
        $form->add('submit', 'submit');

        $form->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Опрос успешно обновлен');

            return $this->redirectToRoute('admin_survey_list');
        }

        return ['form' => $form->createView(), 'survey' => $survey];
    }
}
