<?php

namespace Tests;

use Nxu\NestedSet\Eloquent\NestedSetCollection;
use Tests\Database\SampleCategorySeeder;
use Tests\TestClasses\TestCategory;
use Tests\TestClasses\TestCategoryOrderedByTitle;

class NestedSetCollectionTest extends IntegrationTestWithDb
{
    /** @test */
    public function orders_into_nested_set_collection()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $categories = TestCategory::all()->toHierarchy();

        $this->assertInstanceOf(NestedSetCollection::class, $categories);

    }

    /**
     * @test
     * @depends orders_into_nested_set_collection
     */
    public function childless_nodes_have_empty_children_collection()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $jackets = TestCategory::where('title', 'Jackets')->first();

        $this->assertInstanceOf(NestedSetCollection::class, $jackets->children);
        $this->assertCount(0, $jackets->children);
    }

    /**
     * @test
     * @depends orders_into_nested_set_collection
     * @depends childless_nodes_have_empty_children_collection
     */
    public function it_orders_into_correct_hierarchy()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        $categories = TestCategory::all()->toHierarchy();

        $this->assertCount(1, $categories);

        $clothing = $categories->first();
        $this->assertEquals('Clothing', $clothing->title);
        $this->assertCount(2, $clothing->children);

        $mens = $clothing->children->first();
        $this->assertEquals('Men\'s', $mens->title);
        $this->assertCount(1, $mens->children);

        $suits = $mens->children->first();
        $this->assertEquals('Suits', $suits->title);
        $this->assertCount(2, $suits->children);

        $slacks = $suits->children->first();
        $this->assertEquals('Slacks', $slacks->title);
        $this->assertCount(0, $slacks->children);
    }

    /**
     * @test
     * @depends it_orders_into_correct_hierarchy
     */
    public function it_sorts_into_correct_order()
    {
        $seeder = new SampleCategorySeeder();
        $seeder->seedWithOnlyParentIds();

        // TestCategory orders by id, Slacks > Jackets
        $categories = TestCategory::all()->toHierarchy();

        $clothing = $categories->first();
        $mens = $clothing->children->first();
        $suits = $mens->children->first();

        $this->assertEquals('Slacks', $suits->children()->first()->title);

        // TestCategoryOrderdByTitle orders by title, Jackets > Slacks
        $categories = TestCategoryOrderedByTitle::all()->toHierarchy();

        $clothing = $categories->first();
        $mens = $clothing->children->first();
        $suits = $mens->children->first();

        $this->assertEquals('Jackets', $suits->children()->first()->title);
    }
}
