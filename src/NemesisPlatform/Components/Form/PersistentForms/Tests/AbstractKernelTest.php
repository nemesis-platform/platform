<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 01.06.2015
 * Time: 10:56
 */

namespace NemesisPlatform\Components\Form\PersistentForms\Tests;

use NemesisPlatform\Components\Form\PersistentForms\PersistentFormsBundle;
use NemesisPlatform\Components\Test\Testing\AbstractDatabaseTest;

abstract class AbstractKernelTest extends AbstractDatabaseTest
{
    protected static function createKernel(array $options = [])
    {
        return new TestKernel(
            'test',
            true,
            [
                new PersistentFormsBundle(),
            ],
            [
                __DIR__.'/fixtures/config.yml',
            ]
        );
    }
}
