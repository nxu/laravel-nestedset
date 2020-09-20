<?php

namespace Tests\Database;

use Tests\TestClasses\TestCategory;

/**
 * @see https://commons.wikimedia.org/wiki/File:NestedSetModel.svg
 */
class SampleCategorySeeder
{
    public function seedWithOnlyParentIds()
    {
        $clothing = $this->createNode(null, 'Clothing');

        $mens = $this->createNode($clothing->id, 'Men\'s');
        $womens = $this->createNode($clothing->id, 'Women\'s');

        $suits = $this->createNode($mens->id, 'Suits');
        $slacks = $this->createNode($suits->id, 'Slacks');
        $jackets = $this->createNode($suits->id, 'Jackets');

        $dresses = $this->createNode($womens->id, 'Dresses');
        $skirts = $this->createNode($womens->id, 'Skirts');
        $blouses = $this->createNode($womens->id, 'blouses');

        $eveningGowns = $this->createNode($dresses->id, 'Evening Gowns');
        $sunDresses = $this->createNode($dresses->id, 'Sun Dresses');
    }

    protected function createNode($parentId, $title)
    {
        return TestCategory::create([
            'title' => $title,
            'parent_id' => $parentId,
        ]);
    }
}
