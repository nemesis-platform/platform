<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 06.03.2015
 * Time: 15:54
 */

namespace NemesisPlatform\Modules\Game\Core\Entity\Round;

use DateTime;

interface TimedRoundInterface
{
    /** @return DateTime */
    public function getStart();

    /** @return DateTime */
    public function getFinish();

    /** @return bool */
    public function isStarted();

    /** @return bool */
    public function isFinished();

    /** @return bool */
    public function isPaused();
}
