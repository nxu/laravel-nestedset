<?php

namespace Tests;

use Nxu\NestedSet\Builders\SimpleEloquentBuilder;
use Tests\Database\SampleCategorySeeder;
use Tests\TestClasses\TestCategory;

class NestedSetQueryTest extends IntegrationTestWithDb
{
    /** @test */
    public function getAllLeaves_returns_all_leaves()
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
}
