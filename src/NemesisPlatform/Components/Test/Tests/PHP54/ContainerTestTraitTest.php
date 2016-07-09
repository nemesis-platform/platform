<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-09-23
 * Time: 21:45
 */

namespace NemesisPlatform\Components\Tests\PHP54;

use NemesisPlatform\Components\Testing\ContainerTestTrait;

/**
 * Class ContainerTestTraitTest
 * @package NemesisPlatform\Components\Tests\PHP53
 * @requires PHP 5.4
 */
class ContainerTestTraitTest extends \PHPUnit_Framework_TestCase
{
    use ContainerTestTrait;

    public function testContainer()
    {
        $container = $this->buildContainer(array(new FixtureBundle()));
        self::assertTrue($container->hasParameter('fixture.bundle'));
        self::assertTrue($container->getParameter('fixture.bundle'));
    }
}
