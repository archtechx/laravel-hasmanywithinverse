<?php

declare(strict_types=1);

namespace Stancl\HasManyWithInverse;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasManyWithInverseRelationship extends HasMany
{
    /** @var string */
    protected $relationToParent;

    /** @var array */
    protected $config;

    public function __construct(Builder $query, Model $parent, string $foreignKey, string $localKey, string $relationToParent, array $config)
    {
        $this->relationToParent = $relationToParent;
        $this->config = $config;

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

            if ($this->config('setRelationOnCreation', true)) {
                $instance->setRelation($this->relationToParent, $this->getParent());
            }

            $instance->save();
        });
    }

    public function getResults()
    {
        $results = parent::getResults();

        if ($this->config('setRelationOnResolution', true)) {
            $results->each->setRelation($this->relationToParent, $this->getParent());
        }

        return $results;
    }

    protected function config(string $key, $default)
    {
        if (! isset($this->config[$key])) {
            return $default;
        }

        if (is_callable($this->config[$key])) {
            return $this->config[$key]();
        }

        return $this->config[$key];
    }
}
