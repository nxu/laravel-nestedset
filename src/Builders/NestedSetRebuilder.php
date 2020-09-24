<?php

namespace Nxu\NestedSet\Builders;

use Nxu\NestedSet\Eloquent\Node;

class NestedSetRebuilder
{
    public function rebuild(Node $model): void
    {
        $model->getConnection()->transaction(function () use ($model) {
            foreach ($this->getRootNodes($model) as $node) {
                $left = $this->buildNode($node, $left ?? 1, 0);
            }
        });
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

    protected function getRootNodes(Node $model)
    {
        return $model->newNestedSetQuery()
            ->whereNull($model->getQualifiedParentIdColumn())
            ->get();
    }
}
