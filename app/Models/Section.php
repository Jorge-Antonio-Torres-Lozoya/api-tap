<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Section extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'sections';

    protected $fillable = ['code', 'name', 'slug'];

    public $timestamps = true;
    const UPDATED_AT = null;
}
