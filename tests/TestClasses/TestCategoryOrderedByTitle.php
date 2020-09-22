<?php

namespace Tests\TestClasses;

class TestCategoryOrderedByTitle extends TestCategory
{
    public $table = 'test_categories';
    public $orderBy = 'title';
}
