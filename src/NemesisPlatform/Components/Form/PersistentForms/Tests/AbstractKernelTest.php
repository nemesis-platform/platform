<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 01.06.2015
 * Time: 10:56
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Tests;

use NemesisPlatform\Components\Form\PersistentForms\PersistentFormsBundle;
use NemesisPlatform\Components\Testing\FixtureTestCase;

abstract class AbstractKernelTest extends FixtureTestCase
{
    protected static function createKernel(array $options = array())
    {
        return new TestKernel(
            'test',
            true,
            array(
                new PersistentFormsBundle(),
            ),
            array(
                __DIR__.'/fixtures/config.yml'
            )
        );
    }
}
