<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 18:05
 */

namespace NemesisPlatform\Modules\Game\Core\Controller\User;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Modules\Game\Core\Entity\DraftRecord;
use NemesisPlatform\Modules\Game\Core\Entity\RatingRecord;
use NemesisPlatform\Modules\Game\Core\Entity\Round\PeriodicRound;
use NemesisPlatform\Modules\Game\Core\Entity\Round\Round;
use NemesisPlatform\Modules\Game\Core\Entity\Round\ScenarioRoundInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class RatingController
 *
 * @package NemesisPlatform\Modules\Game\Core\Controller\User
 * @Route("/ratings")
 */
class RatingController extends Controller
{

    /**
     * @return mixed
     * @Route("", name="game_core_view_rating")
     * @Method("GET")
     * @Template()
     */
    public function viewAction()
    {
        return [];
    }


    /**
     * @Route("/table", name="module_ratings_table")
     * @Method("GET")
     * @param Request $request
     *
     * @return Response
     */
    public function getRatingsAction(Request $request)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $round = $manager->getRepository(PeriodicRound::class)->find($request->query->getInt('round'));
        if (!$round) {
            throw new NotFoundHttpException('Раунд не найден');
        }

        $draft = $manager->getRepository(DraftRecord::class)->findBy(
            [
                'round'  => $round,
                'league' => $request->query->getInt('league'),
                'group'  => $request->query->getInt('group'),
            ],
            ['company' => 'ASC']
        );

        $teams = array_map(
            function (DraftRecord $record) {
                return $record->getTeam();
            },
            $draft
        );

        $ratingRecords = $manager->getRepository(RatingRecord::class)->findBy(
            ['period' => $round->getPeriods()->toArray(), 'team' => $teams]
        );

        $result = [];
        foreach ($draft as $draftRecord) {
            $this->formDraftRecord($result, $draftRecord, $round, $ratingRecords);
        }

        return new JsonResponse($result);
    }

    /**
     * @param array          $result
     * @param DraftRecord    $draftRecord
     * @param Round          $round
     * @param RatingRecord[] $ratingRecords
     */
    private function formDraftRecord(
        array &$result,
        DraftRecord $draftRecord,
        Round $round,
        array $ratingRecords
    ) {
        $result[$draftRecord->company]['id']   = $draftRecord->getTeam()->getID();
        $result[$draftRecord->company]['name'] = $draftRecord->getTeam()->getName();

        if ($round instanceof ScenarioRoundInterface) {
            $result[$draftRecord->company]['data'][0] = $round->getScenario() ?
                $round->getScenario()->getValue('zeroRating') : '';
        }

        foreach ($ratingRecords as $record) {
            if ($record->getPeriod()->isRatingsPublished() && $record->getTeam() === $draftRecord->getTeam()) {
                $result[$draftRecord->company]['data'][$record->getPeriod()->getPosition()] = $record->getValue();
            }
        }
    }
}
