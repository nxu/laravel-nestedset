<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nxu\NestedSet\NestedSetServiceProvider;
use Orchestra\Testbench\TestCase;
use Tests\Database\SampleCategorySeeder;
use Tests\TestClasses\TestCategory;

class NodeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_handles_parent_relationship()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $dresses = TestCategory::where('title', 'Dresses')->first();

        $this->assertInstanceOf(BelongsTo::class, $dresses->parent());
        $this->assertEquals('Women\'s', $dresses->parent->title);
    }

    /** @test */
    public function it_handles_children_relationship()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $dresses = TestCategory::where('title', 'Dresses')->first();

        $this->assertInstanceOf(HasMany::class, $dresses->children());
        $this->assertCount(2, $dresses->children);
        $this->assertContains('Evening Gowns', $dresses->children->pluck('title'));
    }

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
