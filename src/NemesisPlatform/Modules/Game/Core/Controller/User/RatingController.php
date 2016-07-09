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
use NemesisPlatform\Modules\Game\Core\Entity\Round\ScenarioRoundInterface;
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
     * @Template()
     */
    public function viewAction()
    {
        return [];
    }


    /**
     * @Route("/table", name="module_ratings_table")
     * @param Request $request
     *
     * @return Response
     */
    public function getRatingsAction(Request $request)
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        $roundId = $request->query->get('round');
        $league  = $request->query->get('league');
        $group   = $request->query->get('group');

        $round = $manager->getRepository(PeriodicRound::class)->find($roundId);

        if (!$round) {
            throw new NotFoundHttpException('Раунд не найден');
        }

        $draft = $manager->getRepository(DraftRecord::class)->findBy(
            ['round' => $round, 'league' => $league, 'group' => $group],
            ['company' => 'ASC']
        );

        $teams = [];
        foreach ($draft as $dr) {
            $teams[] = $dr->getTeam();
        }

        $ratingRecords = $manager->getRepository(RatingRecord::class)->findBy(
            ['period' => $round->getPeriods()->toArray(), 'team' => $teams]
        );

        $ratingArray = [];

        foreach ($draft as $dr) {
            $ratingArray[$dr->company]['id']   = $dr->getTeam()->getID();
            $ratingArray[$dr->company]['name'] = $dr->getTeam()->getName();

            if ($round instanceof ScenarioRoundInterface) {
                $ratingArray[$dr->company]['data'][0] = $round->getScenario() ?
                    $round->getScenario()->getValue('zeroRating') : '';
            }

            foreach ($ratingRecords as $record) {
                if ($record->getPeriod()->isRatingsPublished()) {
                    if ($record->getTeam() === $dr->getTeam()) {
                        $ratingArray[$dr->company]['data'][$record->getPeriod()->getPosition()]
                            = $record->getValue();
                    }
                }
            }
        }

        return new JsonResponse($ratingArray);
    }
}
