<?php

namespace Stancl\HasManyWithInverse\Tests;

class ConfigTest extends TestCase
{
    /** @test */
    public function setting_relation_on_creation_can_be_disabled()
    {
        /** @var ParentModel $parent */
        $parent = (new class extends ParentModel {
            public function children()
            {
                return $this->hasManyWithInverse(ChildModel::class, 'parent', 'parent_id', null, [
                    'setRelationOnCreation' => false,
                ]);
            }
        })::create([]);

        /** @var ChildModel $child */
        $child = $parent->children()->create([]);

        $this->assertFalse($child->relationLoaded('parent'));
    }

    /** @test */
    public function setting_relation_on_resolution_can_be_disabled()
    {
        /** @var ParentModel $parent */
        $parent = (new class extends ParentModel {
            public function children()
            {
                return $this->hasManyWithInverse(ChildModel::class, 'parent', 'parent_id', null, [
                    'setRelationOnResolution' => false,
                ]);
            }
        })::create([]);

        /** @var ChildModel $child */
        $child = $parent->children()->create([]);

        $this->assertFalse($parent->children->first()->relationLoaded('parent'));
    }

    /** @test */
    public function config_value_can_be_a_closure()
    {
        /** @var ParentModel $parent */
        $parent = (new class extends ParentModel {
            public function children()
            {
                return $this->hasManyWithInverse(ChildModel::class, 'parent', 'parent_id', null, [
                    'setRelationOnResolution' => function () {
                        return false;
                    },
                ]);
            }
        })::create([]);

        /** @var ChildModel $child */
        $child = $parent->children()->create([]);

        $this->assertFalse($parent->children->first()->relationLoaded('parent'));
    }
}
