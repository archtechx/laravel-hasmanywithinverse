<?php

namespace Stancl\HasManyWithInverse\Tests;

use Illuminate\Database\Eloquent\Model;
use Stancl\HasManyWithInverse\HasManyWithInverse;

class ParentModel extends Model
{
    use HasManyWithInverse;

    protected $table = 'parents';
    public $timestamps = [];
    protected $guarded = [];

    public function children()
    {
        return $this->HasManyWithInverse(ChildModel::class, 'parent', 'parent_id');
    }
}
