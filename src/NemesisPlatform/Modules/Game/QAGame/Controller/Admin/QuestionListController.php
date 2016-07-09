<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.06.2015
 * Time: 17:38
 */

namespace NemesisPlatform\Modules\Game\QAGame\Controller\Admin;

use NemesisPlatform\Modules\Game\QAGame\Entity\QuestionList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class QuestionListController
 *
 * @package NemesisPlatform\Modules\Game\QAGame\Controller\Admin
 * @Route("/question_list")
 */
class QuestionListController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @Template()
     * @Route("/create", name="admin_module_qa_game_qlist_create")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm('module_qa_game_question_list');
        $form->add('submit', 'submit', ['label' => 'Создать опрос']);
        $form->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $list = $form->getData();
            $manager->persist($list);
            $manager->flush();

            return $this->redirectToRoute('module_qa_game_admin_dashboard');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request      $request
     * @param QuestionList $list
     *
     * @return Response
     *
     * @Template()
     * @Route("/{list}/edit", name="admin_module_qa_game_qlist_edit")
     */
    public function editAction(Request $request, QuestionList $list)
    {
        $form = $this->createForm('module_qa_game_question_list', $list);
        $form->add('submit', 'submit', ['label' => 'Обновить опрос']);
        $form->handleRequest($request);

        $manager = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $list = $form->getData();
            $manager->persist($list);
            $manager->flush();

            return $this->redirectToRoute('module_qa_game_admin_dashboard');
        }

        return [
            'form' => $form->createView(),
            'list' => $list,
        ];
    }

    /**
     * @param QuestionList $list
     *
     * @return Response
     *
     * @Route("/{list}/delete", name="admin_module_qa_game_qlist_delete")
     */
    public function deleteAction(QuestionList $list)
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($list);
        $manager->flush();

        $this->get('session')->getFlashBag()->add('warning', 'Опросник был успешно удален');

        return $this->redirectToRoute('admin_module_qa_game_dashboard_index');
    }
}
