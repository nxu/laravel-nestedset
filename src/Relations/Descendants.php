<?php

namespace Nxu\NestedSet\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Nxu\NestedSet\Node;

class Descendants extends Relation
{
    private $includeSelf;

    public function __construct(Node $parent, $includeSelf = false)
    {
        parent::__construct($parent->newNestedSetQuery(), $parent);
        $this->includeSelf = $includeSelf;
    }

    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where(function ($q) {
                $leftColumn = $this->related->getQualfiedLeftColumn();
                $parentLeft = $this->parent->getLeft();
                $parentRight = $this->parent->getRight();

                $q->where($leftColumn, $this->greaterThan(), $parentLeft)
                    ->where($leftColumn, $this->lessThan(), $parentRight);
            });
        }
    }

    public function addEagerConstraints(array $models)
    {
        $whereIn = $this->whereInMethod($this->parent, $this->parent->getKeyName());

        $this->query->{$whereIn}(
            $this->parent->getKey(),
            $this->getKeys($models, $this->parent->getKey())
        );
    }

    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    public function match(array $models, Collection $results, $relation)
    {
        // TODO: Implement match() method.
    }

    public function getResults()
    {
        return $this->query->get();
    }

    private function lessThan()
    {
        return $this->includeSelf ? '<=' : '<';
    }

    private function greaterThan()
    {
        return $this->includeSelf ? '>=' : '>';
    }
}
