<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.06.2015
 * Time: 14:58
 */

namespace NemesisPlatform\Modules\Game\QAGame\Controller\Admin;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Modules\Game\QAGame\Entity\QARound;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RoundController
 *
 * @package NemesisPlatform\Modules\Game\QAGame\Controller\Admin
 * @Route("/round")
 */
class RoundController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/create", name="admin_module_qa_game_round_create")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @return Response|array
     */
    public function createAction(Request $request)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm('module_qa_game_round');
        $form->add('submit', 'submit', ['label' => 'Создать раунд']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $round = $form->getData();
            $manager->persist($round);
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Успешное создание раунда');

            return $this->redirectToRoute('module_qa_game_admin_dashboard');
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param $round
     *
     * @Route("/{round}/delete", name="admin_module_qa_game_round_delete")
     * @Method("GET")
     *
     * @return RedirectResponse
     */
    public function deleteAction(QARound $round)
    {
        $manager = $this->getDoctrine()->getManager();

        $manager->remove($round);
        $manager->flush();

        $this->get('session')->getFlashBag()->add('warning', 'Раунд был успешно удален');

        return $this->redirectToRoute('module_qa_game_admin_dashboard');
    }

    /**
     * @Route("/{round}/edit", name="admin_module_qa_game_round_edit")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @param Request $request
     * @param         $round
     *
     * @return Response|array
     */
    public function editAction(Request $request, QARound $round)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm('module_qa_game_round', $round)
            ->add('submit', 'submit', ['label' => 'Обновить раунд']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Раунд успешно обновлен');

            return $this->redirectToRoute('module_qa_game_admin_dashboard');
        }

        return [
            'form'  => $form->createView(),
            'round' => $round,
        ];
    }

    /**
     * @param QARound $round
     *
     * @return array
     * @Route("/{round}/view", name="admin_module_qa_game_round_view")
     * @Method("GET")
     * @Template()
     */
    public function viewAction(QARound $round)
    {
        return [
            'round' => $round,
        ];
    }
}
