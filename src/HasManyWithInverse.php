<?php

declare(strict_types=1);

namespace Stancl\HasManyWithInverse;

use Illuminate\Database\Eloquent\Model;

trait HasManyWithInverse
{
    public function hasManyWithInverse($related, $inverse, $foreignKey = null, $localKey = null, $config = [])
    {
        /** @var Model $this */
        $instance = $this->newRelatedInstance($related);
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        $localKey = $localKey ?: $this->getKeyName();

        return new HasManyWithInverseRelationship($instance->newQuery(), $this, $instance->getTable() . '.' . $foreignKey, $localKey, $inverse, $config);
    }
}
