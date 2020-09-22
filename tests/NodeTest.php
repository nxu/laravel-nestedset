<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Nxu\NestedSet\Eloquent\NestedSetCollection;
use Tests\Database\SampleCategorySeeder;
use Tests\TestClasses\TestCategory;

class NodeTest extends IntegrationTestWithDb
{
    use RefreshDatabase;

    /** @test */
    public function it_includes_table_name_in_qualified_column_names()
    {
        $node = new TestCategory();

        $this->assertEquals('test_categories.parent_id', $node->getQualifiedParentIdColumn());
        $this->assertEquals('test_categories.left', $node->getQualifiedLeftColumn());
        $this->assertEquals('test_categories.right', $node->getQualifiedRightColumn());
        $this->assertEquals('test_categories.depth', $node->getQualifiedDepthColumn());
        $this->assertEquals('test_categories.id', $node->getQualifiedOrderColumn());

        $node->setTable('faketable');

        $this->assertEquals('faketable.parent_id', $node->getQualifiedParentIdColumn());
        $this->assertEquals('faketable.left', $node->getQualifiedLeftColumn());
        $this->assertEquals('faketable.right', $node->getQualifiedRightColumn());
        $this->assertEquals('faketable.depth', $node->getQualifiedDepthColumn());
        $this->assertEquals('faketable.id', $node->getQualifiedOrderColumn());
    }

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

    /** @test */
    public function it_creates_nested_set_collection_instead_of_default()
    {
        $testCategory = new TestCategory();

        $this->assertInstanceOf(NestedSetCollection::class, $testCategory->newCollection());
    }
}
