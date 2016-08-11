<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 10.03.2015
 * Time: 11:04
 */

namespace NemesisPlatform\Modules\Game\Core\Controller\User;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Game\Entity\SeasonedSite;
use NemesisPlatform\Modules\Game\Core\Entity\DraftRecord;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DraftRound;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController extends Controller
{
    /**
     * @Route("/game/rating_team_search/{round}", name="nemesis_rating_team_search")
     * @Method("GET")
     *
     * @param Request    $request
     * @param DraftRound $round
     *
     * @return JsonResponse
     */
    public function findTeamAutocompleteAction(Request $request, DraftRound $round)
    {
        if (!$request->request->get('term')) {
            return new JsonResponse([]);
        }

        /** @var SeasonedSite $site */
        $site = $this->get('site.provider')->getSite();

        if (!$site->getSeasons()->contains($round->getSeason())) {
            throw new NotFoundHttpException('Round not found');
        }

        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();
        $query   = $manager->getRepository(DraftRecord::class)->createQueryBuilder('dr')
                           ->addSelect('dr')
                           ->addSelect('t')
                           ->leftJoin('dr.team', 't')
                           ->andWhere('t.name LIKE :term')->setParameter('term', '%'.$request->request->get('term').'%')
                           ->andWhere('dr.round = :round')->setParameter('round', $round);

        $records = $query->getQuery()->getResult();

        return new JsonResponse(
            array_map(
                function (DraftRecord $draft) {
                    $team = $draft->getTeam();

                    return [
                        'label'  => $team->getName(),
                        'id'     => $team->getID(),
                        'league' => $draft->league,
                        'group'  => $draft->group,
                    ];
                },
                $records
            )
        );
    }
}
