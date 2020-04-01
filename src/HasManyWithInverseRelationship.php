<?php

namespace Stancl\HasManyWithInverse;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyWithInverseRelationship extends HasMany
{
    protected string $relationToParent;

    public function __construct(Builder $query, Model $parent, $foreignKey, $localKey, $relationToParent)
    {
        $this->relationToParent = $relationToParent;

        parent::__construct($query, $parent, $foreignKey, $localKey);
    }

    public function create(array $attributes = [])
    {
        return tap($this->related->newInstance($attributes), function ($instance) {
            $this->setForeignAttributesForCreate($instance);

            $instance->setRelation($this->relationToParent, $this->getParent());

            $instance->save();
        });
    }
}
