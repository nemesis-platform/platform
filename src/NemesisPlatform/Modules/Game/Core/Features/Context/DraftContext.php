<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 13.03.2015
 * Time: 18:55
 */

namespace NemesisPlatform\Modules\Game\Core\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Game\Entity\Team;
use PHPUnit_Framework_Assert;

class DraftContext extends RawMinkContext
{
    use KernelDictionary;

    /**
     * @When /^I fill in "([^"]*)" with draft:$/
     * @param           $field
     * @param TableNode $table
     */
    public function iFillInWithDraft($field, TableNode $table)
    {
        $field = $this->fixStepArgument($field);

        $value = '';

        /** @var EntityManagerInterface $manager */
        $manager = $this->getContainer()->get('doctrine')->getManager();

        foreach ($table->getColumnsHash() as $row) {
            $team = $manager->getRepository(Team::class)->findOneBy(['name' => $row['team']]);
            PHPUnit_Framework_Assert::assertNotNull($team, sprintf('Команда %s не найдена', $row['team']));

            $value .= sprintf('%d %d %d %d', $team->getID(), $row['league'], $row['group'], $row['company']).PHP_EOL;
        }


        $value = $this->fixStepArgument($value);
        $this->getSession()->getPage()->fillField($field, $value);
    }

    /**
     * Returns fixed step argument (with \\" replaced back to ").
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }
}
