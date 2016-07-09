<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 12.03.2015
 * Time: 18:09
 */

namespace NemesisPlatform\Modules\Game\Core\Controller\Admin;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Modules\Game\Core\Entity\DraftRecord;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DraftRound;
use NemesisPlatform\Game\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DraftController
 *
 * @Route("/round/{round}/draft")
 */
class DraftController extends Controller
{
    /**
     * @param Request    $request
     * @param DraftRound $round
     * @Route("/import", name="admin_game_core_draft__import")
     *
     * @return Response
     * @Template()
     */
    public function importAction(Request $request, DraftRound $round)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder(
            null,
            [
                'action' => $this->generateUrl(
                    'admin_game_core_draft__import',
                    ['round' => $round->getId()]
                ),
            ]
        )
                     ->add(
                         'team_list',
                         'textarea',
                         ['label' => 'Жеребьевка', 'attr' => ['placeholder' => 'id l g c']]
                     )
                     ->add('submit', 'submit', ['label' => 'Обновить жеребьевку'])
                     ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $lines = explode("\n", $form->get('team_list')->getData());
            for ($i = 0; $i < count($lines); $i++) {
                if ($data = sscanf($lines[$i], "%d\t%d\t%d\t%d")) {
                    list($tid, $league, $group, $company) = $data;
                    $team = $manager->getRepository(Team::class)->find($tid);
                    if (!$team) {
                        continue;
                    }

                    $draft = new DraftRecord($round, $team);
                    $draft->setTeam($team);
                    $draft->setRound($round);

                    if ($round->hasTeam($team)) {
                        $draft = $round->getTeamDraft($team);
                    }

                    $team->setFrozen(true);
                    $draft->league  = $league ?: 0;
                    $draft->group   = $group ?: 0;
                    $draft->company = $company ?: 0;

                    $manager->persist($draft);
                }
            }
            $manager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Жеребьевка обновлена');

            return $this->redirect(
                $request->headers->get(
                    'referer',
                    $this->generateUrl('admin_module_topaz_dashboard_index')
                )
            );
        }

        return ['form' => $form->createView()];
    }

    /**
     * @param Request    $request
     * @param DraftRound $round
     * @Route("/clear", name="admin_game_core_draft__clear")
     *
     * @return Response
     */
    public function clearAction(Request $request, DraftRound $round)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        foreach ($round->getDraft() as $draft) {
            $manager->remove($draft);
        }

        $manager->flush();

        $this->get('session')->getFlashBag()->add('warning', 'Жеребьевка очищена');

        return $this->redirect(
            $request->headers->get(
                'referer',
                $this->generateUrl('admin_module_topaz_dashboard_index')
            )
        );
    }

    /**
     * @param Request    $request
     * @param DraftRound $round
     * @param Team       $team
     *
     * @Route("/{team}/drop", name="admin_game_core_draft__drop_team")
     * @return Response
     */
    public function dropAction(Request $request, DraftRound $round, Team $team)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $draft = $manager->getRepository(DraftRecord::class)->findOneBy(
            [
                'round' => $round,
                'team'  => $team,
            ]
        );

        $manager->remove($draft);
        $manager->flush();

        $this->get('session')->getFlashBag()->add('warning', 'Команда удалена');

        return $this->redirect(
            $request->headers->get(
                'referer',
                $this->generateUrl('admin_module_topaz_dashboard_index')
            )
        );
    }

    /**
     * @param Request $request
     * @param DraftRound $round
     *
     * @Route("/defreeze", name="admin_game_core_draft__unfreeze")
     * @return Response
     */
    public function unfreezeAction(Request $request, DraftRound $round)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        foreach ($round->getTeams() as $team) {
            $team->setFrozen(false);
        }
        $manager->flush();

        return $this->redirect(
            $request->headers->get(
                'referer',
                $this->generateUrl('admin_module_topaz_dashboard_index')
            )
        );
    }
}
