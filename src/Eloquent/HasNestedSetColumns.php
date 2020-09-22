<?php

namespace Nxu\NestedSet\Eloquent;

use Nxu\NestedSet\NestedSet;

trait HasNestedSetColumns
{
    public $orderBy = 'id';

    public function getOrder()
    {
        return $this->getAttribute($this->getOrderColumn());
    }

    public function getOrderColumn(): string
    {
        return $this->orderBy;
    }

    public function getQualifiedOrderColumn(): string
    {
        return $this->getTable() . '.' . $this->getOrderColumn();
    }

    public function getLeft(): string
    {
        return $this->getAttribute($this->getLeftColumn());
    }

    public function getLeftColumn(): string
    {
        return NestedSet::LEFT;
    }

    public function setLeftColumn($left): self
    {
        return $this->setAttribute($this->getLeftColumn(), $left);
    }

    public function getQualifiedLeftColumn(): string
    {
        return $this->getTable() . '.' . $this->getLeftColumn();
    }

    public function getRight()
    {
        return $this->getAttribute($this->getRightColumn());
    }

    public function getRightColumn(): string
    {
        return NestedSet::RIGHT;
    }

    public function setRightColumn($right): self
    {
        return $this->setAttribute($this->getRightColumn(), $right);
    }

    public function getQualifiedRightColumn(): string
    {
        return $this->getTable() . '.' . $this->getRightColumn();
    }

    public function getParentId()
    {
        return $this->getAttribute($this->getParentIdColumn());
    }

    public function getParentIdColumn(): string
    {
        return NestedSet::PARENT_ID;
    }

    public function setParentIdColumn($parentId): self
    {
        return $this->setAttribute($this->getParentIdColumn(), $parentId);
    }

    public function getQualifiedParentIdColumn(): string
    {
        return $this->getTable() . '.' . $this->getParentIdColumn();
    }

    public function getDepth()
    {
        return $this->getAttribute($this->getDepthColumn());
    }

    public function getDepthColumn(): string
    {
        return NestedSet::DEPTH;
    }

    public function setDepthColumn($depth): self
    {
        return $this->setAttribute($this->getDepthColumn(), $depth);
    }

    public function getQualifiedDepthColumn(): string
    {
        return $this->getTable() . '.' . $this->getDepthColumn();
    }
}
