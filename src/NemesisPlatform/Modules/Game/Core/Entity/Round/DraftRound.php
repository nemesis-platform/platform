<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-05-01
 * Time: 17:39
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Round;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use NemesisPlatform\Modules\Game\Core\Entity\DraftRecord;
use NemesisPlatform\Game\Entity\Team;

class DraftRound extends Round implements FilteredRoundInterface
{
    /** @var DraftRecord[]|ArrayCollection */
    private $draft;

    public function __construct()
    {
        $this->draft = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|DraftRecord[]
     */
    public function getDraft()
    {
        return $this->draft;
    }

    /**
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return boolean True if has team in draft
     */
    public function hasTeam(Team $team)
    {
        return !$this->draft->matching(Criteria::create()->where(Criteria::expr()->eq('team', $team)))->isEmpty();
    }

    /**
     * @param \NemesisPlatform\Game\Entity\Team $team
     *
     * @return DraftRecord|null
     */
    public function getTeamDraft(Team $team)
    {
        if (!$team) {
            return null;
        }

        return $this->draft->matching(Criteria::create()->where(Criteria::expr()->eq('team', $team)))->first();
    }


    /**
     * @return Team[]
     */
    public function getTeams()
    {
        return $this->draft->map(
            function (DraftRecord $draft) {
                return $draft->getTeam();
            }
        )->toArray();
    }
}
