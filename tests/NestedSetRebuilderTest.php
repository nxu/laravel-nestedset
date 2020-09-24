<?php

namespace Tests;

use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\Database\SampleCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nxu\NestedSet\Builders\NestedSetRebuilder;
use Nxu\NestedSet\NestedSet;
use Tests\TestClasses\TestCategory;
use Tests\TestClasses\TestCategoryOrderedByTitle;

/**
 * @see https://commons.wikimedia.org/wiki/File:NestedSetModel.svg
 */
class NestedSetRebuilderTest extends IntegrationTestWithDb
{
    use RefreshDatabase;

    /** @test */
    public function it_builds_tree_correctly()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        /** @var NestedSetRebuilder $builder */
        $builder = $this->app->make(NestedSetRebuilder::class);

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

    /** @test */
    public function test_it_wraps_build_process_in_transaction()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $testCategory = new TestCategory();

        $testCategory->getConnection()->setEventDispatcher(
            Event::fake([TransactionBeginning::class, TransactionCommitted::class])
        );

        $builder = $this->app->make(NestedSetRebuilder::class);
        $builder->rebuild($testCategory);

        Event::assertDispatched(TransactionBeginning::class, 1);
        Event::assertDispatched(TransactionCommitted::class, 1);
    }

    /** @test */
    public function it_builds_tree_according_to_sort_order()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        TestCategory::create([
            'title' => 'Shoes',
            'parent_id' => null
        ]);

        $oldId = TestCategory::where('title', 'Clothing')->value('id');

        TestCategory::where('title', 'Clothing')->update(['id' => 999999]);
        TestCategory::where('parent_id', $oldId)->update(['parent_id' => 999999]);

        /** @var NestedSetRebuilder $builder */
        $builder = $this->app->make(NestedSetRebuilder::class);

        $builder->rebuild(new TestCategoryOrderedByTitle());

        $nodes = DB::table('test_categories')
            ->whereIn('title', ['Clothing', 'Shoes', 'Slacks', 'Jackets'])
            ->get()
            ->keyBy('title');

        $clothing = $nodes->get('Clothing');
        $shoes = $nodes->get('Shoes');
        $slacks = $nodes->get('Slacks');
        $jackets = $nodes->get('Jackets');

        $this->assertTrue($clothing->left < $shoes->left);
        $this->assertTrue($jackets->left < $slacks->left);
    }
}
