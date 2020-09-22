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
        // Initialize an empty collection as relation value ('children').
        $models->each(function (Node $model) use ($relation) {
            $model->setRelation($relation, new static());
        });

        $nestedKeys = [];

        // The `models` collection uses the primary key of the models as keys.
        // This makes it easy to move each model into the children relation
        // of its parent, as defined by the parent_id property.
        foreach ($models as $child) {
            $parentId = $child->getParentId();

            if (! is_null($parentId) && $models->has($parentId)) {
                $models->get($parentId)->{$relation}->push($child);
                $nestedKeys[] = $child->getKey();
            }
        }

        // As all the nested models are now someone's child, delete them
        // from the original collection, thus only keeping the topmost
        // level of the hierarchy.
        foreach ($nestedKeys as $nestedKey) {
            $models->forget($nestedKey);
        }

        return $models;
    }
}
