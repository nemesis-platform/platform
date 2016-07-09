<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 15.05.2015
 * Time: 14:35
 */

namespace NemesisPlatform\Modules\Game\Core\Repository;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Modules\Game\Core\Entity\Round\Round;
use NemesisPlatform\Game\Entity\Season;

class RoundRepository extends EntityRepository
{

    /**
     *
     * @param \NemesisPlatform\Game\Entity\Season $season
     *
     * @return Round[]
     */
    public function findActiveRounds(Season $season)
    {
        /** @var Round[] $rounds */
        $rounds = $this
            ->createQueryBuilder('r')
            ->select('r')
            ->andWhere('r.active = :active')->setParameter('active', true)
            ->andWhere('r.season = :season')->setParameter('season', $season)
            ->getQuery()->getResult();

        return $rounds;
    }
}
