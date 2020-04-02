<?php

namespace Stancl\HasManyWithInverse\Tests;

class ModelResolutionTest extends TestCase
{
    /** @test */
    public function children_have_the_parent_relationship_automatically_set_when_being_resolved()
    {
        /** @var ParentModel $parent */
        $parent = ParentModel::create([]);

        ChildModel::create(['parent_id' => $parent->id]);

        /** @var ChildModel $child */
        $child = $parent->children->first();

        $this->assertTrue($child->relationLoaded('parent'));
        $this->assertSame($parent->id, $child->getRelations()['parent']->id);
    }
}