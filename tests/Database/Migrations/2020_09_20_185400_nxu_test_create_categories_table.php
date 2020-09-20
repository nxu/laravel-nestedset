<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NxuTestCreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('title');
            $table->nestedSet();
        });
    }

    public function down()
    {
        Schema::drop('test_categories');
    }
}
