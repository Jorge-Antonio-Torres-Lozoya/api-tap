<?php

namespace App\Models;

use App\Enums\AuditActionEnum;
use Illuminate\Support\Facades\Auth;
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
        'ip_address',
        'user_agent',
    ];

    const UPDATED_AT = null;

    protected $casts = [
        'action'        => AuditActionEnum::class,
        'previous_data' => 'array',
        'current_data'  => 'array',
    ];

    /**
     * Persist an audit entry, enriching it with request context (IP, user-agent).
     */
    public static function record(array $data): void
    {
        $context = app()->runningInConsole()
            ? ['ip_address' => null, 'user_agent' => null]
            : ['ip_address' => request()->ip(), 'user_agent' => request()->userAgent()];

        static::create(array_merge($context, $data));
    }

    /**
     * Record an authentication event (login, logout, failed login, password reset).
     */
    public static function recordAuth(AuditActionEnum $action, ?string $userId, array $context = []): void
    {
        static::record([
            'collection'    => 'auth',
            'document_id'   => $userId,
            'action'        => $action,
            'previous_data' => null,
            'current_data'  => $context ?: null,
            'performed_by'  => $userId ?? Auth::id(),
        ]);
    }
}
