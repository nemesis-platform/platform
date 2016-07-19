<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 04.03.2015
 * Time: 13:49
 */

namespace NemesisPlatform\Modules\Game\Core\Controller\User;

use NemesisPlatform\Modules\Game\Core\Entity\Round\DecisionRoundInterface;
use NemesisPlatform\Modules\Game\Core\Entity\Round\Round;
use NemesisPlatform\Game\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GameController
 *
 * @package NemesisPlatform\Modules\Game\Core\Controller\User
 */
class GameController extends Controller
{
    /**
     * @Route("/decision", name="core_game_user_decision")
     * @Route("/decision/round/{round}/team/{team}", name="core_game_user_decision_round_team")
     * @Route("/decision/round/{round}", name="core_game_user_decision_round")
     * @Route("/decision/team/{team}", name="core_game_user_decision_team")
     * @Method({"GET","POST"})
     * @Template()
     *
     * @param Request                                     $request
     *
     * @param Round|null                                  $round
     * @param \NemesisPlatform\Game\Entity\Team|null $team
     *
     * @return Response
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws NotFoundHttpException
     * @throws AccessDeniedHttpException
     */
    public function decisionAction(Request $request, Round $round = null, Team $team = null)
    {
        $manager = $this->getDoctrine()->getManager();

        $game = $this->get('nemesis.game_manager');

        $options = $game->getActiveRoundOptions($round, $team);

        /** @var DecisionRoundInterface|Round $round */
        $round = $options['round'];

        $form = $this->createForm($round->createDecision()->getFormType(), null, $options);
        $form->add('submit', 'submit', ['label' => 'Сохранить решение']);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $manager->persist($form->getData());
            $manager->flush();
            $this->addFlash('success', 'Успешное сохранение формы');

            return $this->redirectToRoute('core_game_user_decision');
        } elseif ($form->isSubmitted()) {
            $this->addFlash('danger', 'Форма не была сохранена. Проверьте форму на наличие ошибок');
        }

        return [
            'form' => $form->createView(),
            'round' => $options['round'],
            'team' => $options['team'],
            'period' => $options['period'],
        ];
    }
}
