<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-10
 * Time: 22:19
 */

namespace NemesisPlatform\Game\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Season;

class ParticipantRepository extends EntityRepository
{

    /**
     * @param                                          $dataTablesRequest
     * @param                                          $fields
     * @param Season|null                              $season
     * @param SiteInterface                            $site
     * @param bool                                     $everyone
     *
     * @return array
     */
    public function jqueryDataTableFetch(
        $dataTablesRequest,
        $fields,
        SiteInterface $site,
        Season $season = null,
        $everyone = true
    ) {
        $aColumns = [];
        foreach ($fields as $value) {
            $aColumns[] = $value;
        }


        $builder = $this
            ->createQueryBuilder('ud')
            ->leftJoin('ud.user', 'u')
            ->leftJoin('ud.category', 'category')
            ->leftJoin('ud.season', 'season')
            ->leftJoin('season.site', 'site');


        $builder->andWhere('site = :site')->setParameter('site', $site);

        if ($season) {
            $builder->andWhere('ud.season = :season')->setParameter('season', $season);
        }

        if ($everyone) {
            $builder->leftJoin('u.phone', 'p');
        } else {
            $builder->join('u.phone', 'p');
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
            $term = $dataTablesRequest['sSearch'];
//            $term = mb_convert_encoding($dataTablesRequest['sSearch'], 'utf-8');

            $aLike = [];
            $iMax  = count($aColumns);
            for ($i = 0; $i < $iMax; $i++) {
                if (isset($dataTablesRequest['bSearchable_'.$i])
                    && $dataTablesRequest['bSearchable_'.$i] === 'true'
                ) {
                    $aLike[] = $builder->expr()->like($aColumns[$i], '\'%'.$term.'%\'');
                }
            }
            if (count($aLike) > 0) {
                $builder->andWhere(new Expr\Orx($aLike));
            } else {
                unset($aLike);
            }
        }

        $ftBuilder = clone $builder;

        /*
         * SQL queries
         * Get data to display
         */
        $query   = $builder
            ->select('ud')
            ->leftJoin('ud.teams', 't')
            ->groupBy('ud')
            ->getQuery();
        $rResult = $query->getResult();

        $iFilteredTotal = $ftBuilder->setMaxResults(null)->setFirstResult(null)->select("count('ud')")->getQuery()
            ->getSingleScalarResult();


        /* Total data set length */
        $rtCb = $this
            ->createQueryBuilder('ud')
            ->select("count(ud)")
            ->leftJoin('ud.user', 'u')
            ->leftJoin('ud.category', 'category')
            ->leftJoin('ud.season', 'season')
            ->leftJoin('season.site', 'site');

        if ($season) {
            $rtCb->andWhere('ud.season = :season')->setParameter('season', $season);
        }

        if ($everyone) {
            $rtCb->leftJoin('u.phone', 'p');
        } else {
            $rtCb->join('u.phone', 'p');
        }
        $aResultTotal = $rtCb
            ->getQuery()
            ->getScalarResult();
        $iTotal
                      = $aResultTotal[0][1];


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

    /**
     * @param Season $season
     *
     * @return Participant[]
     */
    public function prefetchSeason(Season $season)
    {
        $builder = $this->createQueryBuilder('sd')
            ->select(
                'sd',
                'user',
                'values',
                'field',
                'district',
                'phone',
                'category',
                'league'
            )
            ->leftJoin('sd.user', 'user')
            ->leftJoin('sd.values', 'values')
            ->leftJoin('values.field', 'field')
            ->leftJoin('user.phone', 'phone')
            ->leftJoin('sd.category', 'category')
            ->leftJoin('category.league', 'league')
            ->andWhere('sd.season = :season')
            ->orderBy('sd.created', 'DESC');

        $result = $builder->setParameter('season', $season)
            ->getQuery()->getResult();

        return $result;
    }
}
