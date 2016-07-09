<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 19.08.2014
 * Time: 17:03
 */

namespace NemesisPlatform\Core\Account\Tests\DataSet;

use DateTime;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use NemesisPlatform\Core\Account\Entity\User;

class UsersDataset implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('ru_RU');
        $ru_faker = new Faker\Provider\ru_RU\Person($faker);

        $admin = new User('admin@test', 'secret', $ru_faker->firstNameMale(),$ru_faker->lastName());
        $admin->setMiddlename($ru_faker->middleNameMale());
        $admin->setBirthdate(new DateTime($faker->date()));
        $admin->setStatus(User::EMAIL_CONFIRMED);

        $manager->persist($admin);

        $manager->flush();

        $user = new User('user@test','secret', $ru_faker->firstNameMale(),$ru_faker->lastName());
        $user->setMiddlename($ru_faker->middleNameMale());
        $user->setBirthdate(new DateTime($faker->date()));
        $user->setStatus(User::EMAIL_CONFIRMED);

        $manager->persist($user);
        $manager->flush();
        $manager->clear();
    }
}
