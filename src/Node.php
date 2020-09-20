<?php

namespace Nxu\NestedSet;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    public function parent()
    {
        return $this->belongsTo(Node::class, 'parent_id');
    }
}
