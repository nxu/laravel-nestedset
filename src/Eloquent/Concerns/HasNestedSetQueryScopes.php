<?php

namespace Nxu\NestedSet\Eloquent\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @method Builder descendants()
 * @method Builder descendantsAndSelf()
 * @method Builder ancestors()
 * @method Builder ancestorsAndSelf()
 * @method Builder leaves()
 */
trait HasNestedSetQueryScopes
{
    protected function scopeDescendants(Builder $query)
    {
        return $this->buildDescendantsQuery($query, false);
    }

    protected function scopeDescendantsAndSelf(Builder $query)
    {
        return $this->buildDescendantsQuery($query, true);
    }

    protected function scopeAncestors(Builder $query)
    {
        return $this->buildAncestorsQuery($query, false);
    }

    protected function scopeAncestorsAndSelf(Builder $query)
    {
        return $this->buildAncestorsQuery($query, true);
    }

    protected function scopeLeaves(Builder $query)
    {
        $left = $this->getQualifiedLeftColumn();
        $right = $this->getQualifiedRightColumn();

        return $query->descendants()->where(DB::raw("$right - $left"), '=', 1);
    }

    private function buildDescendantsQuery(Builder $query, $includeSelf = false)
    {
        return $query->where(function (Builder $query) use ($includeSelf) {
            $gt = $includeSelf ? '>=' : '>';
            $lt = $includeSelf ? '<=' : '<';

            $query->where($this->getQualifiedLeftColumn(), $gt, $this->getLeft())
                ->where($this->getQualifiedLeftColumn(), $lt, $this->getRight());
        });
    }

    private function buildAncestorsQuery(Builder $query, $includeSelf = false)
    {
        return $query->where(function (Builder $query) use ($includeSelf) {
            $gt = $includeSelf ? '>=' : '>';
            $lt = $includeSelf ? '<=' : '<';

            $query->where($this->getQualifiedLeftColumn(), $lt, $this->getLeft())
                ->where($this->getQualifiedRightColumn(), $gt, $this->getLeft());
        });
    }
}
