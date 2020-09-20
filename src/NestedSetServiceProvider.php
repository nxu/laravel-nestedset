<?php

namespace Nxu\NestedSet;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class NestedSetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blueprint::macro('nestedSet', function ($table) {
            NestedSet::addColumns($table);
        });

        Blueprint::macro('dropNestedSet', function ($table) {
            NestedSet::dropColumns($table);
        });

        Blueprint::macro('dropNestedSetIndex', function ($table) {
            NestedSet::dropIndex($table);
        });
    }
}
