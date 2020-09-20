<?php

namespace Nxu\NestedSet\Relations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class Descendants extends Relation
{
    public function addConstraints()
    {
    }

    public function addEagerConstraints(array $models)
    {
        // TODO: Implement addEagerConstraints() method.
    }

    public function initRelation(array $models, $relation)
    {
        // TODO: Implement initRelation() method.
    }

    public function match(array $models, Collection $results, $relation)
    {
        // TODO: Implement match() method.
    }

    public function getResults()
    {
        // TODO: Implement getResults() method.
    }
}
