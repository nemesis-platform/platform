<?php
/**
 * Created by PhpStorm.
 * User: Pavel
 * Date: 2014-11-09
 * Time: 15:25
 */

namespace NemesisPlatform\Components\Tests\PHP53;

use NemesisPlatform\Components\Testing\FixtureTestCase;
use NemesisPlatform\Components\Testing\KernelForTest;

class FixtureTestCaseTest extends FixtureTestCase
{
    public static function createKernel(array $options = array())
    {
        return new KernelForTest('test', true);
    }

    /**
     * @dataset NemesisPlatform\Components\Tests\PHP53\SampleFixture
     * @dataset NemesisPlatform\Components\Tests\PHP53\SampleFixture
     */
    public function testFixtureLoading()
    {
        static::assertCount(1, $this->getFixtures());
    }

    public function testNoFixturesLoaded()
    {
        static::assertEmpty($this->getFixtures());
    }

    /**
     * @dataset NemesisPlatform\Components\Tests\PHP53\SampleDependentFixture
     */
    public function testDependedFixtures()
    {
        static::assertCount(2, $this->getFixtures());
    }

    public function sampleDataProvider()
    {
        return array(
            'sample set 1' => array(1, 2, 3),
            'sample set 2' => array(4, 5, 9),
        );
    }

    /**
     * @dataset NemesisPlatform\Components\Tests\PHP53\SampleFixture
     * @dataProvider sampleDataProvider
     * @param $a
     * @param $b
     * @param $c
     */
    public function testDataProviders($a, $b, $c)
    {
        static::assertEquals($c, $a + $b);
        static::assertCount(1, $this->getFixtures());
    }
}
