<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait HasSoftDelete
{
    use SoftDeletes;

    public function isDeleted(): bool
    {
        return $this->trashed();
    }
}
