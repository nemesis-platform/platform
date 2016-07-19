<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-10
 * Time: 22:19
 */

namespace NemesisPlatform\Game\Repository;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\Account\Entity\Tag;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Helpers\DatatablesHelper;

class TeamRepository extends EntityRepository
{
    /**
     * @param Tag $tag
     *
     * @return Team[]
     */
    public function findByTag(Tag $tag)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->leftJoin('t.tags', 'tags')
            ->where('tags = :tag')->setParameter('tag', $tag)
            ->getQuery()->getResult();
    }


    /**
     * @param Season $season
     *
     * @return Team[]
     */
    public function prefetchSeason(Season $season)
    {
        $builder = $this->createQueryBuilder('t')
            ->select('t,m,md,c,cd,cp,mp')
            ->leftJoin('t.members', 'md')
            ->leftJoin('md.user', 'm')
            ->leftJoin('t.captain', 'cd')
            ->leftJoin('cd.user', 'c')
            ->leftJoin('c.phone', 'cp')
            ->leftJoin('m.phone', 'mp')
            ->andWhere('t.season = :season')
            ->orderBy('t.form_date', 'ASC');

        $result = $builder->setParameter('season', $season)
            ->getQuery()->getResult();

        return $result;
    }

    /**
     * @param               $dataTablesRequest
     * @param               $fields
     * @param SiteInterface $site
     * @param Season|null   $season
     *
     * @return array
     */
    public function jqueryDataTableFetch($dataTablesRequest, $fields, SiteInterface $site, Season $season = null)
    {
        $aColumns = [];
        foreach ($fields as $value) {
            $aColumns[] = $value;
        }


        $builder = $this
            ->createQueryBuilder('t')
            ->select('t,l,r,cd,cu')
            ->leftJoin('t.captain', 'cd')
            ->leftJoin('t.league', 'l')
            ->leftJoin('t.season', 'season')
            ->leftJoin('season.site', 'site')
            ->leftJoin('cd.user', 'cu');

        $builder->andWhere('site = :site')->setParameter('site', $site);

        if ($season) {
            $builder->andWhere('t.season = :season')->setParameter('season', $season);
        }

        $rResult = DatatablesHelper::applyDatatablesFilter($dataTablesRequest, $builder, $aColumns);

        $iFilteredTotal = $builder->setMaxResults(null)->setFirstResult(null)->select("count(t)")->getQuery()
            ->getSingleScalarResult();

        /* Total data set length */
        $rtCb = $this
            ->createQueryBuilder('t')
            ->select("count(t)")
            ->join('t.captain', 'cd')
            ->join('cd.user', 'cu');

        if ($season) {
            $rtCb->andWhere('t.season = :season')->setParameter('season', $season);
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

    /**
     * @param User     $user
     * @param Season[] $seasons
     *
     * @return Team[]
     */
    public function getInvites(User $user, array $seasons)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->join('t.invites', 'd')
            ->join('d.user', 'u')
            ->andWhere('t.season in (:seasons)')->setParameter('seasons', $seasons)
            ->andWhere('u = :user')->setParameter('user', $user)
            ->getQuery()->getResult();
    }

    /**
     * @param User     $user
     * @param Season[] $seasons
     *
     * @return Team[]
     */
    public function getRequestes(User $user, array $seasons)
    {
        return $this->createQueryBuilder('t')
            ->select('t')
            ->join('t.requests', 'd')
            ->join('d.user', 'u')
            ->andWhere('t.season in (:seasons)')->setParameter('seasons', $seasons)
            ->andWhere('u = :user')->setParameter('user', $user)
            ->getQuery()->getResult();
    }
}
