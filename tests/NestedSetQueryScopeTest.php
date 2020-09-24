<?php

namespace Tests;

use Nxu\NestedSet\Builders\NestedSetRebuilder;
use Tests\Database\SampleCategorySeeder;
use Tests\TestClasses\TestCategory;

class NestedSetQueryScopeTest extends IntegrationTestWithDb
{
    /** @test */
    public function descendants_scopes_descendant_nodes()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $builder = $this->app->make(NestedSetRebuilder::class);
        $builder->rebuild(new TestCategory());

        $mens = TestCategory::where('title', 'Men\'s')->first();

        $descendants = $mens->descendants()->get()->pluck('title');

        $this->assertCount(3, $descendants);
        $this->assertContains('Suits', $descendants);
        $this->assertContains('Slacks', $descendants);
        $this->assertContains('Jackets', $descendants);
    }

    /** @test */
    public function descendants_and_self_scopes_descendant_nodes_including_self()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $builder = $this->app->make(NestedSetRebuilder::class);
        $builder->rebuild(new TestCategory());

        $mens = TestCategory::where('title', 'Men\'s')->first();

        $descendants = $mens->descendantsAndSelf()->get()->pluck('title');

        $this->assertCount(4, $descendants);
        $this->assertContains('Men\'s', $descendants);
        $this->assertContains('Suits', $descendants);
        $this->assertContains('Slacks', $descendants);
        $this->assertContains('Jackets', $descendants);
    }

    /** @test */
    public function ancestors_scopes_ancestor_nodes()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $builder = $this->app->make(NestedSetRebuilder::class);
        $builder->rebuild(new TestCategory());

        $suits = TestCategory::where('title', 'Suits')->first();

        $ancestors = $suits->ancestors()->get()->pluck('title');

        $this->assertCount(2, $ancestors);
        $this->assertContains('Men\'s', $ancestors);
        $this->assertContains('Clothing', $ancestors);
    }

    /** @test */
    public function ancestors_and_self_scopes_ancestor_nodes_including_self()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $builder = $this->app->make(NestedSetRebuilder::class);
        $builder->rebuild(new TestCategory());

        $suits = TestCategory::where('title', 'Suits')->first();

        $ancestors = $suits->ancestorsAndSelf()->get()->pluck('title');

        $this->assertCount(3, $ancestors);
        $this->assertContains('Suits', $ancestors);
        $this->assertContains('Men\'s', $ancestors);
        $this->assertContains('Clothing', $ancestors);
    }

    /** @test */
    public function leaves_scopes_leaf_nodes()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $builder = $this->app->make(NestedSetRebuilder::class);
        $builder->rebuild(new TestCategory());

        $mens = TestCategory::where('title', 'Men\'s')->first();

        $leaves = $mens->leaves()->get()->pluck('title');

        $this->assertCount(2, $leaves);
        $this->assertContains('Slacks', $leaves);
        $this->assertContains('Jackets', $leaves);
    }

    /** @test */
    public function leaves_scope_returns_empty_when_node_is_leaf()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $builder = $this->app->make(NestedSetRebuilder::class);
        $builder->rebuild(new TestCategory());

        $jackets = TestCategory::where('title', 'Jackets')->first();

        $leaves = $jackets->leaves()->get()->pluck('title');

        $this->assertCount(0, $leaves);
    }

    /** @test */
    public function siblings_scope_returns_siblings()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $builder = $this->app->make(NestedSetRebuilder::class);
        $builder->rebuild(new TestCategory());

        $jackets = TestCategory::where('title', 'Jackets')->first();

        $siblings = $jackets->siblings()->get()->pluck('title');

        $this->assertCount(1, $siblings);
        $this->assertContains('Slacks', $siblings);
    }

    /** @test */
    public function siblings_and_self_scope_returns_siblings_and_itself()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $builder = $this->app->make(NestedSetRebuilder::class);
        $builder->rebuild(new TestCategory());

        $jackets = TestCategory::where('title', 'Jackets')->first();

        $siblings = $jackets->siblingsAndSelf()->get()->pluck('title');

        $this->assertCount(2, $siblings);
        $this->assertContains('Slacks', $siblings);
        $this->assertContains('Jackets', $siblings);
    }
}
