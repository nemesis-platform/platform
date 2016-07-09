<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 17.03.2015
 * Time: 12:07
 */

namespace NemesisPlatform\Modules\Game\Core\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use DateTime;
use NemesisPlatform\Modules\Game\Core\Entity\Period;
use NemesisPlatform\Modules\Game\Core\Entity\Round\DraftRound;
use PHPUnit_Framework_Assert;

class FixtureContext implements Context, KernelAwareContext
{
    use KernelDictionary;

    /**
     * @Given /^a period #(\d+) for "([^"]*)" starts "([^"]*)" and finished "([^"]*)"$/
     * @param $position
     * @param $roundName
     * @param $startRef
     * @param $finishRef
     */
    public function aPeriodForStartsAndFinished($position, $roundName, $startRef, $finishRef)
    {
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $round = $manager->getRepository(DraftRound::class)->findOneBy(['name' => $roundName]);
        PHPUnit_Framework_Assert::assertNotNull($round, sprintf('Раунд "%s" не найден', $roundName));

        $period = new Period();
        $period->setStart(new DateTime($startRef));
        $period->setEnd(new DateTime($finishRef));
        $period->setRound($round);
        $period->setPosition($position);
        $manager->persist($period);

        $manager->flush();
    }
}
