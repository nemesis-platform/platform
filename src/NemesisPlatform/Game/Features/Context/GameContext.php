<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-07-08
 * Time: 23:01
 */

namespace NemesisPlatform\Game\Features\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Game\Entity\Team;
use Symfony\Component\Routing\RouterInterface;

class GameContext extends RawMinkContext
{
    use KernelDictionary;

    /**
     * @When /^I view team "([^"]*)"$/
     * @param $teamName
     */
    public function iViewTeam($teamName)
    {
        $team = $this->getEntityManager()->getRepository(Team::class)->findOneBy(['name' => $teamName]);

        \PHPUnit_Framework_Assert::assertNotNull($team, 'No team '.$teamName.' found in the team repository');

        $url = $this->getContainer()->get('router')->generate(
            'team_view',
            ['team' => $team->getID()],
            RouterInterface::ABSOLUTE_PATH
        );

        $this->getSession()->visit($url);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
