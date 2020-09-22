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
 * @method Builder siblings()
 * @method Builder siblingsAndSelf()
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

    protected function scopeSiblings(Builder $query)
    {
        return $query->siblingsAndSelf()
            ->where($this->getKeyName(), '<>', $this->getKey());
    }

    protected function scopeSiblingsAndSelf(Builder $query)
    {
        $parentIdColumn = $this->getQualifiedParentIdColumn();
        $parentId = $this->getParentId();

        return $query->where($parentIdColumn, $parentId);
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
