<?php

namespace Tests;

use Nxu\NestedSet\Builders\SimpleEloquentBuilder;
use Tests\Database\SampleCategorySeeder;
use Tests\TestClasses\TestCategory;

class NestedSetQueryTest extends IntegrationTestWithDb
{
    /** @test */
    public function allLeaves_returns_all_leaves()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $builder = $this->app->make(SimpleEloquentBuilder::class);
        $builder->rebuild(new TestCategory());

        $leaves = TestCategory::allLeaves()->pluck('title');

        $this->assertCount(6, $leaves);
        $this->assertContains('Slacks', $leaves);
        $this->assertContains('Evening Gowns', $leaves);
        $this->assertContains('Blouses', $leaves);
    }

    /** @test */
    public function allRoots_returns_all_roots()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        TestCategory::create([
            'title' => 'Shoes',
            'parent_id' => null
        ]);

        $builder = $this->app->make(SimpleEloquentBuilder::class);
        $builder->rebuild(new TestCategory());

        $roots = TestCategory::allRoots()->pluck('title');

        $this->assertCount(2, $roots);
        $this->assertContains('Clothing', $roots);
        $this->assertContains('Shoes', $roots);
    }
}
