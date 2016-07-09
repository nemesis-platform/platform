<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 11:03
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Round;

use NemesisPlatform\Modules\Game\Core\Entity\Decision;

interface DecisionRoundInterface
{
    /** @return Decision */
    public function createDecision();

    /** @return bool */
    public function isDecisionAvailable();
}
