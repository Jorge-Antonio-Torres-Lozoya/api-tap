<?php

namespace App\Traits;

use App\Models\Counter;

trait GeneratesCode
{
    public static function bootGeneratesCode(): void
    {
        static::creating(function ($model) {
            $model->code = Counter::nextCode($model->getCodePrefix());
        });
    }

    abstract protected function getCodePrefix(): string;
}
