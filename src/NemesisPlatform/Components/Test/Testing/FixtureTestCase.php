<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 4/18/14
 * Time: 12:03 AM
 */

namespace NemesisPlatform\Components\Test\Testing;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

abstract class FixtureTestCase extends AbstractDatabaseTest
{
    /** @var  FixtureInterface[] */
    private $fixtures = array();
    
    /**
     * @param FixtureInterface|FixtureInterface[] $data
     */
    private function loadTestData($data)
    {
        $loader = new ContainerAwareLoader(static::$kernel->getContainer());

        if (!is_array($data)) {
            $data = array($data);
        }

        foreach ($data as $dataSet) {
            $loader->addFixture($dataSet);
        }

        $this->fixtures = $loader->getFixtures();

        $purger = new ORMPurger();
        $executor = new ORMExecutor(
            static::$manager,
            $purger
        );

        $executor->execute($loader->getFixtures());
    }

    public function setUp()
    {
        parent::setUp();

        $this->fixtures = [];
        $annotations    = $this->getAnnotations();

        if (isset($annotations['method']['dataset'])) {
            $dataset_classes = $annotations['method']['dataset'];
            foreach ($dataset_classes as $dataset_class) {
                $fixture = new $dataset_class();
                if (!($fixture instanceof FixtureInterface)) {
                    continue;
                }
                $this->fixtures[] = $fixture;
            }
        }

        static::assertNotNull(AbstractDatabaseTest::$kernel->getContainer());

        $this->loadTestData($this->fixtures);
    }

    /**
     * @return FixtureInterface[]
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }
}
