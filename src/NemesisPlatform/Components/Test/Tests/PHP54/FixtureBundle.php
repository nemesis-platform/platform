<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2015-09-20
 * Time: 13:53
 */

namespace NemesisPlatform\Components\Tests\PHP54;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FixtureBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new FixtureBundleExtension();
    }

}
