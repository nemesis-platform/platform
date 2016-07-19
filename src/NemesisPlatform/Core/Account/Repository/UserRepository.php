<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-10
 * Time: 22:19
 */

namespace NemesisPlatform\Core\Account\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use NemesisPlatform\Core\Account\Entity\Tag;
use NemesisPlatform\Core\Account\Entity\User;

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

        if (isset($dataTablesRequest['iDisplayStart']) && $dataTablesRequest['iDisplayLength'] != '-1') {
            $builder->setFirstResult((int)$dataTablesRequest['iDisplayStart'])
                ->setMaxResults((int)$dataTablesRequest['iDisplayLength']);
        }

        /*
         * Ordering
         */
        if (isset($dataTablesRequest['iSortCol_0'])) {
            $iMax = (int)$dataTablesRequest['iSortingCols'];
            for ($i = 0; $i < $iMax; $i++) {
                if ($dataTablesRequest['bSortable_'.(int)$dataTablesRequest['iSortCol_'.$i]] === 'true') {
                    $builder->orderBy(
                        $aColumns[(int)$dataTablesRequest['iSortCol_'.$i]],
                        $dataTablesRequest['sSortDir_'.$i]
                    );
                }
            }
        }

        /*
           * Filtering
           * NOTE this does not match the built-in DataTables filtering which does it
           * word by word on any field. It's possible to do here, but concerned about efficiency
           * on very large tables, and MySQL's regex functionality is very limited
           */
        if (isset($dataTablesRequest['sSearch']) && $dataTablesRequest['sSearch'] != '') {
            $aLike = [];
            $iMax  = count($aColumns);
            for ($i = 0; $i < $iMax; $i++) {
                if (isset($dataTablesRequest['bSearchable_'.$i]) && $dataTablesRequest['bSearchable_'.$i] == "true") {
                    $aLike[] = $builder->expr()->like($aColumns[$i], '\'%'.$dataTablesRequest['sSearch'].'%\'');
                }
            }
            if (count($aLike) > 0) {
                $builder->andWhere(new Expr\Orx($aLike));
            } else {
                unset($aLike);
            }
        }

        /*
         * SQL queries
         * Get data to display
         */
        $query   = $builder->getQuery();
        $rResult = $query->getResult();


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
