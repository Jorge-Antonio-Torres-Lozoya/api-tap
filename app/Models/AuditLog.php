<?php

namespace App\Models;

use App\Enums\AuditActionEnum;
use MongoDB\Laravel\Eloquent\Model;

class AuditLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'audit_logs';

    protected $fillable = [
        'collection',
        'document_id',
        'action',
        'previous_data',
        'current_data',
        'performed_by',
    ];

    const UPDATED_AT = null;

    protected $casts = [
        'action'        => AuditActionEnum::class,
        'previous_data' => 'array',
        'current_data'  => 'array',
    ];
}
