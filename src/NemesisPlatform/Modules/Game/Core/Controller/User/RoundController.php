<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 18:05
 */

namespace NemesisPlatform\Modules\Game\Core\Controller\User;

use Doctrine\ORM\EntityManager;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DraftRound;
use NemesisPlatform\Modules\Game\Core\Entity\Round\Round;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RoundController extends Controller
{
    /**
     * @Route ("/game/rounds/autocomplete", name="module_rounds_autocomplete")
     * @Method("GET")
     * @return Response
     */
    public function getRoundsAction()
    {
        /** @var EntityManager $manager */
        $manager = $this->getDoctrine()->getManager();

        /** @var \NemesisPlatform\Game\Entity\SeasonedSite $site */
        $site = $this->get('site.manager')->getSite();

        $rounds = $manager->getRepository(Round::class)->findBy(
            ['season' => $site->getSeasons()->toArray()],
            ['season' => 'DESC', 'id' => 'DESC']
        );

        return new Response(
            json_encode(
                array_map(
                    function (DraftRound $round) {
                        return [
                            'id'        => $round->getId(),
                            'title'     => $round->getName(),
                            'season_id' => $round->getSeason()->getId(),
                            'season'    => $round->getSeason()->getName(),
                        ];
                    },
                    $rounds
                )
            )
        );
    }
}
