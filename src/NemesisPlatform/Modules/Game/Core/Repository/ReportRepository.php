<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.06.2015
 * Time: 10:57
 */

namespace NemesisPlatform\Modules\Game\Core\Repository;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Modules\Game\Core\Entity\Period;
use NemesisPlatform\Modules\Game\Core\Entity\Report;

class ReportRepository extends EntityRepository
{
    public function getPeriodReportsCount(Period $period)
    {
        return $this
            ->createQueryBuilder('t')
            ->select('count(distinct r.team)')
            ->from(Report::class, 'r')
            ->andWhere('r.period = :period')->setParameter('period', $period)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
