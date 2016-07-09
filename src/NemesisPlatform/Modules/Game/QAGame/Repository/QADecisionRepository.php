<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 22.06.2015
 * Time: 16:08
 */

namespace NemesisPlatform\Modules\Game\QAGame\Repository;

use Doctrine\ORM\EntityRepository;
use NemesisPlatform\Modules\Game\QAGame\Entity\QADecision;
use NemesisPlatform\Modules\Game\QAGame\Entity\QARound;

class QADecisionRepository extends EntityRepository
{

    /**
     * @param QARound $round
     *
     * @return QADecision[]
     */
    public function getRoundDecisions(QARound $round)
    {
        /** @var QADecision[] $tmpDecisions */
        $tmpDecisions = $this->createQueryBuilder('decision')
                             ->select('decision')
                             ->innerJoin(
                                 'QAGameBundle:QADecision',
                                 'max_decision',
                                 'WITH',
                                 'max_decision.round = decision.round AND max_decision.team = decision.team'
                             )
                             ->where('decision.round = :round')->setParameter('round', $round)
                             ->groupBy('decision.id')
                             ->having('decision.id = MAX(max_decision.id)')
                             ->orderBy('IDENTITY(decision.team)', 'ASC')
                             ->addOrderBy('decision.id', 'ASC')->getQuery()->getResult();

        $decisions = [];

        foreach ($tmpDecisions as $form) {
            $decisions[$form->getTeam()->getID()] = $form;
        }

        return $decisions;
    }
}
