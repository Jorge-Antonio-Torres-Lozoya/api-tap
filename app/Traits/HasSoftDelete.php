<?php

namespace App\Traits;

use MongoDB\Laravel\Eloquent\SoftDeletes;

trait HasSoftDelete
{
    use SoftDeletes;

    public function isDeleted(): bool
    {
        return $this->deleted_at !== null;
    }
}
