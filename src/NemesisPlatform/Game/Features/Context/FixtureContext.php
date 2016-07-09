<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-07-08
 * Time: 22:53
 */

namespace NemesisPlatform\Game\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Game\Entity\League;
use NemesisPlatform\Game\Entity\Participant;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\Team;
use NemesisPlatform\Game\Entity\UserCategory;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class FixtureContext implements Context
{
    use KernelDictionary;

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @Given /^a season in "([^"]*)" with:$/
     * @param           $site
     * @param TableNode $table
     */
    public function aSeasonInWith($site, TableNode $table)
    {
        $manager = $this->getContainer()->get('doctrine')->getManager();

        $site = $manager->getRepository(SiteInterface::class)->findOneBy(['fullName' => $site]);
        \PHPUnit_Framework_Assert::assertNotNull($site);

        foreach ($table->getColumnsHash() as $seasonData) {
            $season = new Season();
            $season->setSite($site);
            $accessor = new PropertyAccessor();
            foreach ($seasonData as $key => $value) {
                $accessor->setValue($season, $key, $value);
            }
            $manager->persist($season);
        }
        $manager->flush();
    }

    /**
     * @Given /^a (combined |)league "([^"]*)" in "([^"]*)"$/
     * @param $combined
     * @param $name
     * @param $season
     */
    public function aLeagueIn($combined, $name, $season)
    {
        $manager = $this->getContainer()->get('doctrine')->getManager();

        $season = $manager->getRepository(Season::class)->findOneBy(['name' => $season]);
        \PHPUnit_Framework_Assert::assertNotNull($season);

        $league = new League();
        $league->setName($name);
        $league->setSeason($season);
        $league->setWithCombined($combined === 'combined ');

        $manager->persist($league);
        $manager->flush();
    }

    /**
     * @Given /^a category "([^"]*)" in "([^"]*)"$/
     * @Given /^a category "([^"]*)" in "([^"]*)" with:$/
     * @param $name
     * @param $league
     * @param $table
     */
    public function aCategoryIn($name, $league, TableNode $table = null)
    {
        $manager = $this->getContainer()->get('doctrine')->getManager();

        $league = $manager->getRepository(League::class)->findOneBy(['name' => $league]);
        \PHPUnit_Framework_Assert::assertNotNull($league);

        $category = new UserCategory();
        $category->setName($name);
        $category->setLeague($league);

        if ($table) {
            foreach ($table->getTable() as $row) {
                foreach ($row as $field) {
                    $fld = $manager->getRepository(AbstractField::class)->findOneBy(
                        ['name' => $field]
                    );
                    \PHPUnit_Framework_Assert::assertNotNull($fld);
                    $category->getFields()->add($fld);
                }
            }
        }

        $manager->persist($category);
        $manager->flush();
    }

    /**
     * @Given /^season data for "([^"]*)" with:$/
     * @param           $seasonName
     * @param TableNode $table
     */
    public function seasonDataWith($seasonName, TableNode $table)
    {
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $season = $manager->getRepository(Season::class)->findOneBy(['name' => $seasonName]);
        \PHPUnit_Framework_Assert::assertNotNull($season);

        foreach ($table->getColumnsHash() as $data) {
            $sData = new Participant();

            $user = $manager->getRepository(User::class)->findOneBy(['email' => $data['user']]);
            \PHPUnit_Framework_Assert::assertNotNull($user);
            $sData->setUser($user);
            $user->getParticipations()->add($sData);

            $category = $manager->getRepository(UserCategory::class)->findOneBy(
                ['name' => $data['category']]
            );
            \PHPUnit_Framework_Assert::assertNotNull($category);
            $sData->setCategory($category);

            $sData->setSeason($season);

            $manager->persist($sData);
        }
        $manager->flush();
    }


    /**
     * @Given /^team "([^"]*)" with captain "([^"]*)" in season "([^"]*)"$/
     * @param           $teamName
     * @param           $captainEmail
     * @param           $seasonName
     * @param TableNode $table
     */
    public function teamWithCaptainInSeason($teamName, $captainEmail, $seasonName, TableNode $table)
    {
        $manager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $season = $manager->getRepository(Season::class)->findOneBy(['name' => $seasonName]);
        \PHPUnit_Framework_Assert::assertNotNull($season, sprintf('Сезон "%s" не найден', $seasonName));

        $captain = $manager->getRepository(User::class)->findOneBy(['email' => $captainEmail]);
        \PHPUnit_Framework_Assert::assertNotNull($captain, sprintf('Капитан "%s" не найден', $captainEmail));

        $cData = $captain->getParticipation($season);
        \PHPUnit_Framework_Assert::assertNotNull(
            $cData,
            sprintf('Капитан не зарегистрирован в сезоне "%s"', $seasonName)
        );

        $team = new Team($teamName, $cData);

        foreach ($table->getColumnsHash() as $row) {
            $member = $manager->getRepository(User::class)->findOneBy(['email' => $row['email']]);
            \PHPUnit_Framework_Assert::assertNotNull($member);
            $sData = $member->getParticipation($season);
            \PHPUnit_Framework_Assert::assertNotNull($sData);
            $team->addMember($sData);
        }

        $manager->persist($team);
        $manager->flush();
    }
}
