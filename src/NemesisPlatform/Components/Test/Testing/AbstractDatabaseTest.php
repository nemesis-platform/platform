<?php
namespace NemesisPlatform\Components\Test\Testing;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\SchemaValidator;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractDatabaseTest extends KernelTestCase
{
    /** @var  KernelInterface */
    protected static $kernel;
    /** @var  EntityManager */
    protected static $manager;

    /**
     * @throws ToolsException
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$kernel = static::createKernel([]);
        static::$kernel->boot();

        $metadata = static::getMetadata();

        $tool = new SchemaTool(static::$manager);
        $tool->dropDatabase();
        $tool->createSchema($metadata);

        $validator = new SchemaValidator(static::$manager);
        $errors    = $validator->validateMapping();

        static::assertCount(
            0,
            $errors,
            implode(
                "\n\n",
                array_map(
                    function ($l) {
                        return implode("\n\n", $l);
                    },
                    $errors
                )
            )
        );
    }

    public static function getMetadata()
    {
        /** @var EntityManagerInterface $em */
        static::$manager = static::$kernel->getContainer()->get('doctrine')->getManager();

        return static::$manager->getMetadataFactory()->getAllMetadata();
    }

    public function setUp()
    {
        parent::setUp();

        static::$kernel = static::createKernel([]);
        static::$kernel->boot();


    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return static::$kernel->getContainer();
    }
}
