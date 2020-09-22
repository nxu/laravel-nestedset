<?php

namespace Nxu\NestedSet\Eloquent;

use Illuminate\Database\Eloquent\Collection;

class NestedSetCollection extends Collection
{
    public function toHierarchy($relation = 'children'): NestedSetCollection
    {
        $sortedModels = collect($this->getDictionary())
            ->sortBy(function (Node $node) {
                return $node->getOrder();
            });

        return new static($this->moveIntoHierarchy($sortedModels, $relation));
    }

    protected function moveIntoHierarchy($models, $relation)
    {
        $models->each(function (Node $model) use ($relation) {
            $model->setRelation($relation, new static());
        });

        $nestedKeys = [];

        foreach ($models as $child) {
            $parentId = $child->getParentId();

            if (! is_null($parentId) && $models->has($parentId)) {
                $models->get($parentId)->{$relation}->push($child);
                $nestedKeys[] = $child->getKey();
            }
        }

        foreach ($nestedKeys as $nestedKey) {
            $models->forget($nestedKey);
        }

        return $models;
    }
}
