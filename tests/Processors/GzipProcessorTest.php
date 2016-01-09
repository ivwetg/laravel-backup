<?php

/*
 * This file is part of Laravel Backup.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vinkla\Tests\Backup\Processors;

use Vinkla\Backup\Processors\GzipProcessor;
use Vinkla\Tests\Backup\AbstractTestCase;
use Vinkla\Tests\Backup\FactoryTrait;
use Zenstruck\Backup\Processor\GzipArchiveProcessor;

/**
 * This is the gzip archive processor test class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class GzipProcessorTest extends AbstractTestCase
{
    use FactoryTrait;

    public function testBootstrap()
    {
        $this->assertInstanceOf(GzipArchiveProcessor::class, $this->getFactory()->bootstrap());
    }

    public function getFactory()
    {
        return new GzipProcessor();
    }
}
