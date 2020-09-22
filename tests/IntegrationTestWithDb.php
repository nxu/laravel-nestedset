<?php

namespace Tests;

use Nxu\NestedSet\NestedSetServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class IntegrationTestWithDb extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        $this->artisan('migrate', ['--database' => 'testing']);
    }

    protected function getPackageProviders($app)
    {
        return [NestedSetServiceProvider::class];
    }
}
