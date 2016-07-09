<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 16:05
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Scenario;

use NemesisPlatform\Modules\Game\Core\Entity\Period;

abstract class PeriodicScenario extends AbstractScenario
{
    public function getValue($key)
    {
        return $this->getPeriodValue($key, null);
    }

    abstract public function getPeriodValue($key, Period $period = null);
}
