<?php
namespace NemesisPlatform\Helpers;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class DatatablesHelper
{

    /**
     * @param array $dataTablesRequest
     * @param QueryBuilder $builder
     * @param array $aColumns
     *
     * @return array
     */
    public static function applyDatatablesFilter(array $dataTablesRequest,QueryBuilder $builder,array $aColumns)
    {
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

        return $rResult;
    }
}
