<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 11.11.2014
 * Time: 15:05
 */

namespace NemesisPlatform\Core\CMS\Features\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelDictionary;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use NemesisPlatform\Components\Form\PersistentForms\Entity\Field\AbstractField;
use NemesisPlatform\Components\MultiSite\Entity\SiteInterface;
use NemesisPlatform\Core\Account\Entity\User;
use NemesisPlatform\Core\CMS\Entity\Block\AbstractBlock;
use NemesisPlatform\Core\CMS\Entity\Block\SiteBlock;
use NemesisPlatform\Core\CMS\Entity\NemesisSite;
use NemesisPlatform\Core\CMS\Entity\News;
use NemesisPlatform\Core\CMS\Entity\Page;
use NemesisPlatform\Core\CMS\Entity\PageRevision;
use NemesisPlatform\Core\CMS\Entity\ProxyPage;
use NemesisPlatform\Game\Entity\Season;
use NemesisPlatform\Game\Entity\SeasonedSite;
use PHPUnit_Framework_Assert;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class FixtureContext implements Context
{
    use KernelDictionary;

    /**
     * @Given /^news entity in "([^"]*)" with$/
     * @param           $seasonName
     * @param TableNode $table
     *
     * @throws \Exception
     */
    public function createNewsEntity($seasonName, TableNode $table)
    {
        $container = $this->getContainer();
        $manager   = $container->get('doctrine.orm.entity_manager');
        $season    = $manager->getRepository(Season::class)->findOneBy(['name' => $seasonName]);
        PHPUnit_Framework_Assert::assertNotNull($season);

        foreach ($table as $row) {
            $ne = new News();
            switch ($row['type']) {
                case 'default':
                    $ne->setType(News::TYPE_DEFAULT);
                    break;
                case 'deferred':
                    $ne->setType(News::TYPE_DEFERRED);
                    break;
                case 'disabled':
                    $ne->setType(News::TYPE_DISABLED);
                    break;
                default:
                    throw new \Exception('Unknown news entry type. Expected to be default, deferred or disabled.');
            }
            $ne->setTitle($row['title']);
            $ne->setBody($row['body']);
            $ne->setSeason($season);
            $ne->setDate(new \DateTime($row['date']));

            $manager->persist($ne);
        }

        $manager->flush();
    }

    /**
     * @Given /^page entity in "([^"]*)" with$/
     * @param           $siteName
     * @param TableNode $table
     *
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     */
    public function createPageEntity($siteName, TableNode $table)
    {
        $container = $this->getContainer();
        $manager   = $container->get('doctrine.orm.entity_manager');
        $site      = $manager->getRepository(SiteInterface::class)->findOneBy(['fullName' => $siteName]);
        PHPUnit_Framework_Assert::assertNotNull($site);

        foreach ($table as $row) {
            /** @var User $author */
            $author = $manager->getRepository(User::class)->findOneBy(['email' => $row['author']]);

            $page = new Page();
            $rev  = new PageRevision();
            $rev->setPage($page);
            $rev->setAuthor($author);
            $rev->setContent($row['content']);

            $page->setAuthor($author);
            $page->setAlias($row['alias']);
            $page->setLastRevision($rev);
            $page->setSite($site);
            $page->setTemplate($row['template']);
            $page->setTitle($row['title']);

            $manager->persist($page);
            $manager->persist($rev);
        }

        $manager->flush();
    }

    /**
     * @Given /^empty database$/
     * @throws ToolsException
     */
    public function emptyDatabase()
    {
        $manager  = $this->getEntityManager();
        $metadata = $manager->getMetadataFactory()->getAllMetadata();
        $tool     = new SchemaTool($manager);
        $tool->dropDatabase();
        $tool->createSchema($metadata);
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @Given /^users with:$/
     * @param TableNode $table
     */
    public function usersWith(TableNode $table)
    {
        $manager = $this->getContainer()->get('doctrine')->getManager();

        $factory = $this->getContainer()->get('security.encoder_factory');

        foreach ($table->getColumnsHash() as $userData) {
            $user = new User($userData['email'], 'secret', $userData['firstname'], $userData['lastname']);
            $user->setStatus(User::EMAIL_CONFIRMED);
            $accessor = new PropertyAccessor();

            foreach ($userData as $key => $value) {
                $accessor->setValue($user, $key, $value);
            }

            $user->setBirthdate(
                new \DateTime(array_key_exists('birthdate', $userData) ? $userData['birthdate'] : 'now')
            );

            if ($user->getPassword()) {
                $user->setPassword(
                    $factory->getEncoder($user)->encodePassword($user->getPassword(), $user->getSalt())
                );
            }

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @Given /^a site with:$/
     * @param TableNode $table
     */
    public function aSiteWith(TableNode $table)
    {
        $manager = $this->getContainer()->get('doctrine')->getManager();

        foreach ($table->getColumnsHash() as $siteData) {
            $site     = new SeasonedSite($siteData['base_url'], $siteData['short_name']);
            $accessor = new PropertyAccessor();
            foreach ($siteData as $key => $value) {
                $accessor->setValue($site, $key, $value);
            }
            $manager->persist($site);
        }

        $manager->flush();
    }

    /**
     * @Given /^a storable form field "([^"]*)" named "([^"]*)" of type "([^"]*)"$/
     * @Given /^a storable form field "([^"]*)" named "([^"]*)" of type "([^"]*) with:"$/
     * @param           $description
     * @param           $name
     * @param           $type
     * @param TableNode $table
     */
    public function aStorableFormFieldOfType($description, $name, $type, TableNode $table = null)
    {
        $manager = $this->getContainer()->get('doctrine')->getManager();

        /** @var AbstractField $fieldType */
        $fieldType = $this->getContainer()->get('scaytrase.stored_forms.fields_registry')->get($type);
        PHPUnit_Framework_Assert::assertNotNull($fieldType);

        $field = clone $fieldType;
        $field->setName($name);
        $field->setTitle($description);
        $manager->persist($field);
        $manager->flush();
    }


    /**
     * @Given /^a proxy page "([^"]*)" routed to "([^"]*)" in site "([^"]*)" with$/
     * @param           $alias       string
     * @param           $route       string
     * @param           $siteName    string
     * @param TableNode $table
     */
    public function aProxyPage($alias, $route, $siteName, TableNode $table)
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->getContainer()->get('doctrine')->getManager();

        $site = $manager->getRepository(SiteInterface::class)->findOneBy(['fullName' => $siteName]);
        PHPUnit_Framework_Assert::assertNotNull($site);

        $page = new ProxyPage();
        $page->setAlias($alias);
        $page->setRoute($route);
        $page->setSite($site);
        $page->setData($table->getRowsHash());

        $manager->persist($page);
        $manager->flush();
    }

    /**
     * @Given /^a "([^"]*)" block for "([^"]*)" weighted (\d+) at "([^"]*)"$/
     * @param string $type
     * @param string $siteName
     * @param string $weight
     */
    public function aBlockForWeighted($type, $siteName, $weight, $area)
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->getContainer()->get('doctrine')->getManager();

        /** @var NemesisSite $site */
        $site = $manager->getRepository(NemesisSite::class)->findOneBy(['fullName' => $siteName]);
        PHPUnit_Framework_Assert::assertNotNull($site);

        /** @var AbstractBlock $block */
        $block = clone $this->getContainer()->get('nemesis.block_registry')->get($type);
        $block->setDescription($type);
        $manager->persist($block);

        $siteBlock = new SiteBlock();
        $siteBlock->setSite($site);
        $siteBlock->setWeight((int)$weight);
        $siteBlock->setArea($area);
        $siteBlock->setBlock($block);
        $site->addBlock($siteBlock);

        $manager->persist($siteBlock);
        $manager->flush();
    }
}
