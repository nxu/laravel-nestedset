<?php

namespace Nxu\NestedSet\Eloquent\Concerns;

use Illuminate\Support\Facades\DB;

trait HasNestedSetQueries
{
    public static function allLeaves()
    {
        $instance = new static;

        $left = $instance->getQualifiedLeftColumn();
        $right = $instance->getQualifiedRightColumn();

        return $instance->where(DB::raw("$right - $left"), '=', 1)
            ->orderBy($instance->getQualifiedOrderColumn())
            ->get();
    }

    public static function allRoots()
    {
        $instance = new static;

        return $instance->whereNull($instance->getQualifiedParentIdColumn())->get();
    }
}
