<?php

namespace Nxu\NestedSet;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;
use Nxu\NestedSet\Builders\NestedSetBuilder;
use Nxu\NestedSet\Builders\SimpleEloquentBuilder;

class NestedSetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blueprint::macro('nestedSet', function () {
            NestedSet::addColumns($this);
        });

        Blueprint::macro('dropNestedSet', function () {
            NestedSet::dropColumns($this);
        });

        Blueprint::macro('dropNestedSetForeign', function () {
            NestedSet::dropForeignKey($this);
        });

        Blueprint::macro('dropNestedSetIndex', function () {
            NestedSet::dropIndex($this);
        });
    }

    public function register()
    {
        $this->app->bind(NestedSetBuilder::class, SimpleEloquentBuilder::class);
    }
}
