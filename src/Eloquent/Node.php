<?php

namespace Nxu\NestedSet\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Nxu\NestedSet\Eloquent\Concerns\HasNestedSetColumns;
use Nxu\NestedSet\Eloquent\Concerns\HasNestedSetQueryScopes;

class Node extends Model
{
    use HasNestedSetColumns, HasNestedSetQueryScopes;

    public function parent()
    {
        return $this->belongsTo(static::class, $this->getParentIdColumn())
            ->orderBy($this->getQualifiedOrderColumn());
    }

    public function children()
    {
        return $this->hasMany(static::class, $this->getParentIdColumn())
            ->orderBy($this->getQualifiedOrderColumn());
    }

    public function newNestedSetQuery()
    {
        return $this->newQuery()->orderBy($this->getQualifiedOrderColumn());
    }

    public function newCollection(array $models = [])
    {
        return new NestedSetCollection($models);
    }
}
