<?php

namespace Nxu\NestedSet\Builders;

use Nxu\NestedSet\Node;

interface NestedSetBuilder
{
    public function rebuild(Node $model): void;
}
