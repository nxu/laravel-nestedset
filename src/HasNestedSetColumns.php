<?php

namespace Nxu\NestedSet;

trait HasNestedSetColumns
{
    public $orderBy = 'id';

    public function getOrderColumn()
    {
        return $this->orderBy;
    }

    public function getQualifiedOrderColumn()
    {
        return "$this->table.$this->orderBy";
    }

    public function getLeftColumn()
    {
        return NestedSet::LEFT;
    }

    public function getQualifiedLeftColumn()
    {
        return $this->table . '.' . $this->getLeftColumn();
    }

    public function getRightColumn()
    {
        return NestedSet::RIGHT;
    }

    public function getQualifiedRightColumn()
    {
        return $this->table . '.' . $this->getRightColumn();
    }

    public function getParentIdColumn()
    {
        return NestedSet::PARENT_ID;
    }

    public function getQualifiedParentIdColumn()
    {
        return $this->table . '.' . $this->getParentIdColumn();
    }

    public function getDepthColumn()
    {
        return NestedSet::DEPTH;
    }

    public function getQualifiedDepthColumn()
    {
        return $this->table . '.' . $this->getDepthColumn();
    }
}
