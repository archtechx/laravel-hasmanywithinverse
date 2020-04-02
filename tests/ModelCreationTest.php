<?php

namespace Stancl\HasManyWithInverse\Tests;

class ModelCreationTest extends TestCase
{
    /** @test */
    public function children_have_the_parent_relationship_automatically_set_when_being_created()
    {
        /** @var ParentModel $parent */
        $parent = ParentModel::create([]);

        /** @var ChildModel $child */
        $child = $parent->children()->create([]);

        $this->assertTrue($child->relationLoaded('parent'));
        $this->assertSame($parent->id, $child->getRelations()['parent']->id);
    }

    /** @test */
    public function children_have_the_parent_relationship_automatically_set_when_being_created_using_createMany()
    {
        /** @var ParentModel $parent */
        $parent = ParentModel::create([]);

        /** @var ChildModel $child1 */
        /** @var ChildModel $child2 */
        [$child1, $child2] = $parent->children()->createMany([[], []]);

        $this->assertTrue($child1->relationLoaded('parent'));
        $this->assertSame($parent->id, $child1->getRelations()['parent']->id);

        $this->assertTrue($child2->relationLoaded('parent'));
        $this->assertSame($parent->id, $child2->getRelations()['parent']->id);
    }

    /** @test */
    public function children_have_the_parent_relationship_automatically_set_in_creating_event()
    {
        $parentId = null;

        /** @var ParentModel $parent */
        $parent = ParentModel::create([]);

        ChildModel::creating(function (ChildModel $child) use (&$parentId) {
            if ($child->relationLoaded('parent')) {
                $parentId = $child->getRelations()['parent']->id;
            }
        });

        $parent->children()->create([]);

        $this->assertSame($parent->id, $parentId);
    }

    /** @test */
    public function children_have_the_parent_relationship_automatically_set_in_saving_event()
    {
        $parentId = null;

        /** @var ParentModel $parent */
        $parent = ParentModel::create([]);

        ChildModel::saving(function (ChildModel $child) use (&$parentId) {
            if ($child->relationLoaded('parent')) {
                $parentId = $child->getRelations()['parent']->id;
            }
        });

        $parent->children()->create([]);

        $this->assertSame($parent->id, $parentId);
    }
}
