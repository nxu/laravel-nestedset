<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Nxu\NestedSet\NestedSet;
use Orchestra\Testbench\TestCase;

class NestedSetTest extends TestCase
{
    /** @test */
    public function nested_set_macro_creates_columns()
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            NestedSet::addColumns($table);
        });

        $this->assertTrue(Schema::hasColumns('test_categories', [
            NestedSet::LEFT,
            NestedSet::RIGHT,
            NestedSet::PARENT_ID,
            NestedSet::DEPTH,
        ]));
    }

    /** @test */
    public function nested_set_macro_creates_index()
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            NestedSet::addColumns($table);
        });

        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes('test_categories');

        $this->assertArrayHasKey('test_categories_left_right_parent_id_index', $indexes);
    }

    /** @test */
    public function nested_set_macro_creates_foreign_key()
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            NestedSet::addColumns($table);
        });

        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        $foreignKeys = $sm->listTableForeignKeys('test_categories');
        $key = $foreignKeys[0];

        $this->assertContains('parent_id', $key->getLocalColumns());
    }

    /** @test */
    public function drop_nested_set_macro_drops_columns()
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            NestedSet::addColumns($table);
        });

        Schema::table('test_categories', function (Blueprint $table) {
            NestedSet::dropColumns($table);
        });

        $this->assertFalse(Schema::hasColumns('test_categories', [
            NestedSet::LEFT,
            NestedSet::RIGHT,
            NestedSet::PARENT_ID,
            NestedSet::DEPTH,
        ]));
    }

    /** @test */
    public function drop_index_macro_drops_index()
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            NestedSet::addColumns($table);
        });

        Schema::table('test_categories', function (Blueprint $table) {
            NestedSet::dropIndex($table);
        });

        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes('test_categories');

        $this->assertArrayNotHasKey('test_categories_left_right_parent_id_index', $indexes);
    }
}
