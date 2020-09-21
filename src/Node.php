<?php

namespace Nxu\NestedSet;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use HasNestedSetColumns;

    public function parent()
    {
        return $this->belongsTo(static::class, NestedSet::PARENT_ID)
            ->orderBy($this->getQualifiedOrderColumn());
    }

    public function children()
    {
        return $this->hasMany(static::class, NestedSet::PARENT_ID)
            ->orderBy($this->getQualifiedOrderColumn());
    }

    public function newNestedSetQuery()
    {
        return $this->newQuery()->orderBy($this->getQualifiedOrderColumn());
    }
}
