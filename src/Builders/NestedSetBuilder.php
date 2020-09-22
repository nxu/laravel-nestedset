<?php

namespace Nxu\NestedSet\Builders;

use Nxu\NestedSet\Eloquent\Node;

interface NestedSetBuilder
{
    public function rebuild(Node $model): void;
}
