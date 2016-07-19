<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-10
 * Time: 22:19
 */

namespace NemesisPlatform\Core\Account\Repository;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Core\Account\Entity\Tag;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Helpers\DatatablesHelper;

class UserRepository extends EntityRepository
{
    /**
     * @param \NemesisPlatform\Core\Account\Entity\Tag $tag
     *
     * @return User[]
     */
    public function findByTag(Tag $tag)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.tags', 'tags')
            ->where('tags = :tag')->setParameter('tag', $tag)
            ->getQuery()->getResult();
    }

    /**
     * @param      $dataTablesRequest
     * @param      $fields
     * @param bool $confirmed
     *
     * @return array
     */
    public function jqueryDataTableFetch($dataTablesRequest, $fields, $confirmed = true)
    {
        $aColumns = [];
        foreach ($fields as $value) {
            $aColumns[] = $value;
        }


        $builder = $this
            ->createQueryBuilder('u')
            ->select("u")
            ->leftJoin('u.phone', 'p');


        if ($confirmed) {
            $builder->andWhere('u.status = :status')->setParameter('status', User::EMAIL_CONFIRMED);
            $builder->andWhere('p.id IS NOT NULL');
        }

        $rResult = DatatablesHelper::applyDatatablesFilter($dataTablesRequest, $builder, $aColumns);

        $iFilteredTotal = $builder
            ->setMaxResults(null)
            ->setFirstResult(null)
            ->select("count('u')")
            ->getQuery()
            ->getSingleScalarResult();


        /* Total data set length */
        $rtCb = $this
            ->createQueryBuilder('u')
            ->select("count(u)")
            ->leftJoin('u.phone', 'p');

        if ($confirmed) {
            $rtCb->andWhere('u.status = :status')->setParameter('status', User::EMAIL_CONFIRMED);
            $rtCb->andWhere('u.phone IS NOT NULL');
        }
        $aResultTotal = $rtCb
            ->getQuery()
            ->getScalarResult();
        $iTotal       = $aResultTotal[0][1];


        /*
         * Output
         */
        $output = [
            "sEcho"                => (int)$dataTablesRequest['sEcho'],
            "iTotalRecords"        => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData"               => [],
        ];

        return ['objects' => $rResult, 'output' => $output];
    }
}
