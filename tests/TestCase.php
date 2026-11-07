<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        // Separate compiled views from the local preview process on Windows.
        $path = dirname(__DIR__).'/storage/framework/views/tests-'.getmypid();
        if (! is_dir($path)) {
            mkdir($path, 0775, true);
        }
        $_ENV['VIEW_COMPILED_PATH'] = $_SERVER['VIEW_COMPILED_PATH'] = $path;
        $app = parent::createApplication();
        $driver = $app['config']->get('database.default');
        $database = $app['config']->get("database.connections.$driver.database");
        if ($driver === 'mysql' && ! str_ends_with($database, '_test')) {
            throw new \RuntimeException('MySQL acceptance tests require a database ending in _test.');
        }
        if ($driver === 'sqlite' && $database !== ':memory:') {
            throw new \RuntimeException('SQLite acceptance tests require an in-memory database.');
        }

        return $app;
    }
}
