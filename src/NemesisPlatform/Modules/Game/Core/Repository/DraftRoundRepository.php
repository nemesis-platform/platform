<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-09-02
 * Time: 22:56
 */

namespace NemesisPlatform\Modules\Game\Core\Repository;

use NemesisPlatform\Modules\Game\Core\Entity\Round\DraftRound;
use NemesisPlatform\Modules\Game\Core\Entity\Round\TimedRoundInterface;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\Team;

class DraftRoundRepository extends RoundRepository
{

    /**
     * Ищет раунд в заданном сезоне для данной команды
     *
     * @param \NemesisPlatform\Game\Entity\Season $season
     * @param \NemesisPlatform\Game\Entity\Team   $team
     *
     * @return DraftRound|null
     */
    public function findRoundForTeam(Season $season, Team $team)
    {
        $currentRound = null;
        foreach ($this->findActiveRounds($season) as $round) {
            /** @var DraftRound|TimedRoundInterface $round */
            /** @var DraftRound $rd */
            $rd = $this->prefetchDraft($round);

            if ($round instanceof TimedRoundInterface && (!$round->isStarted() || $round->isFinished())) {
                continue;
            }

            if ($rd->hasTeam($team)) {
                $currentRound = $round;
                break;
            }
        }

        return $currentRound;
    }


    /**
     * Преподгружает жеребьевку заданного раунда
     *
     * @param DraftRound $round
     *
     * @return DraftRound
     */
    public function prefetchDraft(DraftRound $round)
    {
        $this->createQueryBuilder('r')
             ->select('r', 'd', 't', 'cd', 'c')
             ->leftJoin('r.draft', 'd')
             ->leftJoin('d.team', 't')
             ->leftJoin('t.captain', 'cd')
             ->leftJoin('cd.user', 'c')
             ->where('r = :round')->setParameter('round', $round)
             ->getQuery()->getResult();

        return $round;
    }
}
