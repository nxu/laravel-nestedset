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
    public function scopeDescendants(Builder $query)
    {
        return $this->buildDescendantsQuery($query, false);
    }

    public function scopeDescendantsAndSelf(Builder $query)
    {
        return $this->buildDescendantsQuery($query, true);
    }

    public function scopeAncestors(Builder $query)
    {
        return $this->buildAncestorsQuery($query, false);
    }

    public function scopeAncestorsAndSelf(Builder $query)
    {
        return $this->buildAncestorsQuery($query, true);
    }

    public function scopeLeaves(Builder $query)
    {
        $left = $this->getQualifiedLeftColumn();
        $right = $this->getQualifiedRightColumn();

        return $query->descendants()->where(DB::raw("$right - $left"), '=', 1);
    }

    public function scopeSiblings(Builder $query)
    {
        return $query->siblingsAndSelf()
            ->where($this->getKeyName(), '<>', $this->getKey());
    }

    public function scopeSiblingsAndSelf(Builder $query)
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
