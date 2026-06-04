<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Counter extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'counters';

    protected $fillable = ['model', 'seq'];

    public $timestamps = false;

    public static function nextCode(string $prefix): string
    {
        // findOneAndUpdate with returnDocument:AFTER is the only truly atomic
        // read-increment-return operation in MongoDB — increment()+refresh() has a race condition.
        $result = static::raw(fn($col) => $col->findOneAndUpdate(
            ['model' => $prefix],
            ['$inc'  => ['seq' => 1]],
            [
                'returnDocument' => \MongoDB\Operation\FindOneAndUpdate::RETURN_DOCUMENT_AFTER,
                'upsert'         => true,
            ]
        ));

        return strtoupper($prefix) . '-' . str_pad($result->seq, 4, '0', STR_PAD_LEFT);
    }
}
