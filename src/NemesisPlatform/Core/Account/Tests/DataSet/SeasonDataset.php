<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.08.2014
 * Time: 17:52
 */

namespace NemesisPlatform\Core\Account\Tests\DataSet;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NemesisPlatform\Game\Entity\League;
use NemesisPlatform\Game\Entity\Rule\Participant\CreateTeamAbilityNotification;
use NemesisPlatform\Game\Entity\Rule\Participant\RequestTeamAbilityNotification;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\SeasonedSite;
use NemesisPlatform\Game\Entity\UserCategory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeasonDataset extends AbstractFixture implements
    FixtureInterface,
    DependentFixtureInterface,
    ContainerAwareInterface
{
    /** @var  ContainerInterface */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $site = new SeasonedSite();
        $site->setName('Тестовое окружение');
        $site->setDescription('Окружение для проведения тестов');
        $site->setShortName('test');
        $site->setEmail('admin@test');
        $site->setUrl('localhost');
        $site->setTheme('basic_bootstrap_theme');
        $site->setActive(true);

        $this->addReference('test-site', $site);

        $season = new Season();
        $season->setName('Тестовый сезон');
        $season->setShortName('testseason');
        $season->setActive(true);
        $season->setSite($site);
        $season->setDescription('Тестовый сезон');
        $season->setRegistrationOpen(true);

        $teamCreateRule = new CreateTeamAbilityNotification();
        $teamRequestRule = new RequestTeamAbilityNotification();

        $manager->persist($teamCreateRule);
        $manager->persist($teamRequestRule);

        $season->setRules(
            [
                $teamCreateRule,
                $teamRequestRule,
            ]
        );

        $this->addReference('test-season', $season);

        $pl = new League();
        $pl->setName('Профессиональная лига');
        $pl->setWithCombined(true);
        $this->addReference('prof-league', $pl);

        $pc = new UserCategory();
        $pc->setName('Профессионал');
        $this->addReference('prof-category', $pc);
        $pl->addCategory($pc);

        $sl = new League();
        $sl->setName('Студенческая лига');
        $this->addReference('stud-league', $sl);

        $sc = new UserCategory();
        $sc->setName('Студент');
        $this->addReference('stud-category', $sc);
        $sl->addCategory($sc);

        $fld = $this->container->get('storable_forms.fields.university');

        // TODO: Rewrite
//        $vuz = new UniversityField();
//        $vuz->setType($fld->getType());
//        $vuz->setName('vuz');
//        $vuz->setTitle('ВУЗ');
//        $vuz->setHelpMessage('Ваш вуз');

//        $sc->getFields()->add($vuz);

        $manager->persist($sc);

        $manager->persist($pl);
        $manager->persist($pc);
        $manager->persist($sl);
        $manager->persist($sc);

        $season->addLeague($pl);
        $season->addLeague($sl);

        $manager->persist($season);
        $manager->persist($site);
        $manager->flush();
        $manager->clear();
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            GeoDataSet::class,
        ];
    }
}
