<?php

namespace Nxu\NestedSet;

use Illuminate\Database\Schema\Blueprint;

final class NestedSet
{
    public const PARENT_ID = 'parent_id';
    public const LEFT = 'left';
    public const RIGHT = 'right';
    public const DEPTH = 'depth';

    public static function addColumns(Blueprint $table): void
    {
        $table->unsignedBigInteger(static::LEFT);
        $table->unsignedBigInteger(static::RIGHT);
        $table->unsignedBigInteger(static::PARENT_ID)->nullable();
        $table->unsignedBigInteger(static::DEPTH);

        $table->index([static::LEFT, static::RIGHT, static::PARENT_ID]);
    }

    public static function dropIndex(Blueprint $table): void
    {
        $table->dropIndex([static::LEFT, static::RIGHT, static::PARENT_ID]);
    }

    public static function dropColumns(Blueprint $table): void
    {
        $table->dropColumn([
            static::LEFT,
            static::RIGHT,
            static::PARENT_ID,
            static::DEPTH,
        ]);
    }
}
