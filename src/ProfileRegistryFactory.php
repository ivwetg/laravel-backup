<?php

/*
 * This file is part of Laravel Backup.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vinkla\Backup;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use Zenstruck\Backup\ProfileBuilder;
use Zenstruck\Backup\ProfileRegistry;

/**
 * This is the profile registry factory class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class ProfileRegistryFactory
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The configuration data.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new profile registry factory instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param array $config
     *
     * @return void
     */
    public function __construct(Application $app, array $config)
    {
        $this->config = $config;
        $this->app = $app;
    }

    /**
     * Get the configuration data.
     *
     * @param array $config
     *
     * @return array
     */
    protected function getConfig(array $config)
    {
        $keys = ['sources', 'profiles', 'destinations', 'processors', 'namers'];

        foreach ($keys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new InvalidArgumentException("Missing configuration key [$key].");
            }
        }

        return $config;
    }

    /**
     * Get the profile registry.
     *
     * @return \Zenstruck\Backup\ProfileRegistry
     */
    public function getProfileRegistry()
    {
        $config = $this->getConfig($this->config);

        $registry = new ProfileRegistry();

        foreach ($config['profiles'] as $name => $config) {
            $builder = $this->getProfileBuilder($config);
            $scratchDir = $this->getScratchDir($config);

            $registry->add($builder->create(
                $name,
                $scratchDir,
                $builder->getProcessor(),
                $builder->getNamer(),
                $builder->getSources(),
                $builder->getDestinations()
            ));
        }

        return $registry;
    }

    /**
     * Get the profile builder.
     *
     * @param array $config
     *
     * @return \Zenstruck\Backup\ProfileBuilder
     */
    protected function getProfileBuilder(array $config)
    {
        return new ProfileBuilder(
            $this->getProcessors($config),
            $this->getNamers($config),
            $this->getSources($config),
            $this->getDestinations($config)
        );
    }

    /**
     * Get the sources.
     *
     * @param array $config
     *
     * @return array
     */
    protected function getSources(array $config)
    {
        foreach (array_get($config, 'sources', []) as $source) {
            $sources[] = $this->app->make($source)->create();
        }

        return $sources;
    }

    /**
     * Get the destinations.
     *
     * @param array $config
     *
     * @return array
     */
    protected function getDestinations(array $config)
    {
        foreach (array_get($config, 'destinations', []) as $destination) {
            $destinations[] = $this->app->make($destination)->create();
        }

        return $destinations;
    }

    /**
     * Get the processors.
     *
     * @param array $config
     *
     * @return array
     */
    protected function getProcessors(array $config)
    {
        $config = array_merge($this->config, $config);

        foreach (array_get($config, 'processors', []) as $namer) {
            $processors[] = $this->app->make($namer, $namer);
        }

        return $processors;
    }

    /**
     * Get the namers.
     *
     * @param array $config
     *
     * @return array
     */
    protected function getNamers(array $config)
    {
        $config = array_merge($this->config, $config);

        foreach (array_get($config, 'namers', []) as $namer) {
            $namers[] = $this->app->make($namer, $namer);
        }

        return $namers;
    }

    /**
     * Get the scratch dir path.
     *
     * @param array $config
     *
     * @return string
     */
    protected function getScratchDir(array $config)
    {
        return array_get($config, 'path', storage_path('backups'));
    }
}
