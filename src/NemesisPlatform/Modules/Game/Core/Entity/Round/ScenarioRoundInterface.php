<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 12.03.2015
 * Time: 17:27
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Round;

use NemesisPlatform\Modules\Game\Core\Entity\Scenario\AbstractScenario;

interface ScenarioRoundInterface
{
    /** @return AbstractScenario */
    public function getScenario();
}
