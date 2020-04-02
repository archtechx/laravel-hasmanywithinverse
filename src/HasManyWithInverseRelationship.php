<?php

namespace Stancl\HasManyWithInverse;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyWithInverseRelationship extends HasMany
{
    /**
     * @var string
     */
    protected $relationToParent;

    public function __construct(Builder $query, Model $parent, string $foreignKey, string $localKey, string $relationToParent)
    {
        $this->relationToParent = $relationToParent;

        parent::__construct($query, $parent, $foreignKey, $localKey);
    }

    /**
     * @param array<mixed, mixed> $attributes
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes = [])
    {
        return tap($this->related->newInstance($attributes), function ($instance) {
            $this->setForeignAttributesForCreate($instance);

            $instance->setRelation($this->relationToParent, $this->getParent());

            $instance->save();
        });
    }
}
