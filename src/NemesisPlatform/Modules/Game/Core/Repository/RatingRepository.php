<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.06.2015
 * Time: 10:58
 */

namespace NemesisPlatform\Modules\Game\Core\Repository;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Modules\Game\Core\Entity\Period;
use NemesisPlatform\Modules\Game\Core\Entity\RatingRecord;

class RatingRepository extends EntityRepository
{
    public function getPeriodRatingCount(Period $period)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('count(distinct r.team)')
            ->from(RatingRecord::class, 'r')
            ->andWhere('r.period = :period')->setParameter('period', $period)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
