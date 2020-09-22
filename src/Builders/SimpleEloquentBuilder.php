<?php

namespace Nxu\NestedSet\Builders;

use Nxu\NestedSet\Eloquent\Node;

class SimpleEloquentBuilder implements NestedSetBuilder
{
    public function rebuild(Node $model): void
    {
        $rootNodes = $model->newNestedSetQuery()
            ->whereNull($model->getParentIdColumn())
            ->get();

        $left = 1;

        foreach ($rootNodes as $node) {
            $left = $this->buildNode($node, $left, 0);
        }
    }

    protected function buildNode(Node $model, $left, $depth)
    {
        $children = $model->newNestedSetQuery()
            ->where('parent_id', $model->getKey())
            ->get();

        $model->setLeftColumn($left);
        $model->setDepthColumn($depth);

        foreach ($children as $node) {
            $left = $this->buildNode($node, ++$left, $depth + 1);
        }

        $model->setRightColumn(++$left);
        $model->save();

        return $left;
    }
}
