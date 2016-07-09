<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 17.12.2014
 * Time: 13:30
 */

namespace NemesisPlatform\Core\CMS\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Core\CMS\Entity\News;
use NemesisPlatform\Game\Entity\Season;

class NewsRepository extends EntityRepository
{
    /**
     * @param Season[] $seasons
     * @param null     $count
     *
     * @return array
     */
    public function getLastNews(array $seasons, $count = null)
    {
        $builder = $this->createQueryBuilder('n')
                        ->select('n')
                        ->andWhere('n.season IN (:seasons)')->setParameter('seasons', $seasons)
                        ->andWhere('(n.type = :type_default) OR (n.type = :type_deferred AND n.date < :now)')
                        ->setParameter('type_default', News::TYPE_DEFAULT)
                        ->setParameter('type_deferred', News::TYPE_DEFERRED)
                        ->setParameter('now', new DateTime())
                        ->orderBy('n.date', 'DESC');

        if ($count !== null) {
            $builder->setMaxResults($count);
        }

        return $builder->getQuery()->getResult();
    }
}
