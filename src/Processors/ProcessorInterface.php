<?php

/*
 * This file is part of Laravel Backup.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vinkla\Backup\Processors;

/**
 * This is the processor interface.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
interface ProcessorInterface
{
    /**
     * Create and register the processor.
     *
     * @return mixed
     */
    public function create();
}