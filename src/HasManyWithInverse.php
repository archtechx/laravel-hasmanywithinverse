<?php

namespace Stancl\HasManyWithInverse;

trait HasManyWithInverse
{
    public function hasManyWithInverse($related, $inverse, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasManyWithInverseRelationship($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey, $inverse);
    }
}
