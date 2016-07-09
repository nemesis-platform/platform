<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 25.06.2015
 * Time: 15:20
 */

namespace NemesisPlatform\Game\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Game\Entity\Rule\AbstractRuleEntity;
use NemesisPlatform\Game\Entity\Rule\Participant\CreateTeamAbilityNotification;
use NemesisPlatform\Game\Entity\Rule\Participant\RequestTeamAbilityNotification;
use NemesisPlatform\Game\Entity\Rule\Participant\SingleTeamRule;
use NemesisPlatform\Game\Entity\Rule\Team\MaxMembersCountRule;
use NemesisPlatform\Game\Entity\Rule\Team\MinMembersCountRule;
use NemesisPlatform\Game\Entity\Season;
use PHPUnit_Framework_Assert;

class RulesFixtureContext implements Context
{
    use KernelDictionary;

    /**
     * @Given /^a "([^"]*)" rule for season "([^"]*)"$/
     * @param $seasonName string
     */
    public function createTeamPossibilityRule($type, $seasonName, TableNode $table = null)
    {
        $manager = $this->getEntityManager();

        $season = $manager->getRepository(Season::class)->findOneBy(['name' => $seasonName]);
        PHPUnit_Framework_Assert::assertNotNull($season);

        $rule = $this->createRuleMethod($type, $table ? $table->getRowsHash() : []);
        $rule->setEnabled(true);

        $season->getRules()->add($rule);

        $manager->persist($rule);
        $manager->flush();
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @param       $ruleType
     * @param array $options
     *
     * @return AbstractRuleEntity
     */
    protected function createRuleMethod($ruleType, array $options = [])
    {
        switch ($ruleType) {
            case 'single team':
                return new SingleTeamRule();
            case 'create team':
                return new CreateTeamAbilityNotification();
            case 'join team':
                return new RequestTeamAbilityNotification();
            case 'team max members':
                $rule = new MaxMembersCountRule();
                if (array_key_exists('max', $options)) {
                    $rule->setMax((int)$options['max']);
                }

                return $rule;
            case 'team min members':
                $rule = new MinMembersCountRule();
                if (array_key_exists('min', $options)) {
                    $rule->setMin((int)$options['min']);
                }

                return $rule;
        }

        throw new PendingException('Unknown rule type '.$ruleType);
    }
}
