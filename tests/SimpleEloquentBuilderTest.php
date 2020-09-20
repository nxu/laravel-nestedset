<?php

namespace Tests;

use Tests\Database\SampleCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nxu\NestedSet\Builders\SimpleEloquentBuilder;
use Nxu\NestedSet\NestedSet;
use Nxu\NestedSet\NestedSetServiceProvider;
use Orchestra\Testbench\TestCase;
use Tests\TestClasses\TestCategory;

/**
 * @see https://commons.wikimedia.org/wiki/File:NestedSetModel.svg
 */
class SimpleEloquentBuilderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_builds_tree_correctly()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        /** @var SimpleEloquentBuilder $builder */
        $builder = $this->app->make(SimpleEloquentBuilder::class);

        $builder->rebuild(new TestCategory());

        $tree = TestCategory::all()->keyBy('title');

        $clothing = $tree->get('Clothing');

        $mens = $tree->get('Men\'s');
        $womens = $tree->get('Women\'s');

        $suits = $tree->get('Suits');
        $slacks = $tree->get('Slacks');
        $jackets = $tree->get('Jackets');

        $dresses = $tree->get('Dresses');
        $skirts = $tree->get('Skirts');
        $blouses = $tree->get('Blouses');

        $eveningGowns = $tree->get('Evening Gowns');
        $sunDresses = $tree->get('Sun Dresses');

        // Root level
        $this->assertEquals(1, $clothing->getAttribute(NestedSet::LEFT));
        $this->assertEquals(22, $clothing->getAttribute(NestedSet::RIGHT));
        $this->assertEquals(0, $clothing->getAttribute(NestedSet::DEPTH));

        // Level 1
        $this->assertEquals(2, $mens->getAttribute(NestedSet::LEFT));
        $this->assertEquals(9, $mens->getAttribute(NestedSet::RIGHT));
        $this->assertEquals(1, $mens->getAttribute(NestedSet::DEPTH));

        // Tree
        $this->assertEquals(4, $slacks->getAttribute(NestedSet::LEFT));
        $this->assertEquals(5, $slacks->getAttribute(NestedSet::RIGHT));
        $this->assertEquals(3, $slacks->getAttribute(NestedSet::DEPTH));

        // Multiple depths on same level
        $this->assertEquals(11, $dresses->getAttribute(NestedSet::LEFT));
        $this->assertEquals(16, $dresses->getAttribute(NestedSet::RIGHT));
        $this->assertEquals(2, $dresses->getAttribute(NestedSet::DEPTH));

        $this->assertEquals(17, $skirts->getAttribute(NestedSet::LEFT));
        $this->assertEquals(18, $skirts->getAttribute(NestedSet::RIGHT));
        $this->assertEquals(2, $skirts->getAttribute(NestedSet::DEPTH));
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
