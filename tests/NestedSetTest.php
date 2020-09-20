<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Nxu\NestedSet\NestedSet;
use Orchestra\Testbench\TestCase;

class NestedSetTest extends TestCase
{
    public function test_nested_set_macro_creates_columns()
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

    public function test_nested_set_macro_creates_index()
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            NestedSet::addColumns($table);
        });

        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $sm->listTableIndexes('test_categories');

        $this->assertArrayHasKey('test_categories_left_right_parent_id_index', $indexes);
    }

    public function test_drop_nested_set_macro_drops_columns()
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

    public function test_drop_index_macro_drops_index()
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
