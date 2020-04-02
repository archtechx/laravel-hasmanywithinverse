<?php

namespace Stancl\HasManyWithInverse\Tests;

use Illuminate\Database\Eloquent\Model;

class ChildModel extends Model
{
    protected $table = 'children';
    public $timestamps = [];
    protected $guarded = [];

    public function parent()
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }
}
