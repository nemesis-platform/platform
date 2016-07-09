<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-09-02
 * Time: 22:56
 */

namespace NemesisPlatform\Modules\Game\Core\Repository;

use DateTime;
use NemesisPlatform\Modules\Game\Core\Entity\Round\PeriodicRound;
use NemesisPlatform\Game\Entity\Season;

class PeriodicRoundRepository extends DraftRoundRepository
{
    /**
     *
     * @param Season $season
     *
     * @return PeriodicRound[]
     */
    public function findActiveRounds(Season $season)
    {
        /** @var PeriodicRound[] $rounds */
        $rounds = $this
            ->createQueryBuilder('r')
            ->select('r')
            ->leftJoin('r.periods', 'p')
            ->andWhere('r.active = :active')->setParameter('active', true)
            ->andWhere('r.season = :season')->setParameter('season', $season)
            ->groupBy('r')
            ->andHaving('MIN(p.start) < :now')
            ->andHaving('MAX(p.end) > :now')
            ->setParameter('now', new DateTime())
            ->getQuery()->getResult();

        return $rounds;
    }
}
