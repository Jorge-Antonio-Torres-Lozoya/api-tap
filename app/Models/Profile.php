<?php

namespace App\Models;

use App\Observers\AuditObserver;
use App\Traits\GeneratesCode;
use App\Traits\HasAuditLog;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use MongoDB\Laravel\Eloquent\Model;

#[ObservedBy([AuditObserver::class])]
class Profile extends Model
{
    use HasSoftDelete, GeneratesCode, HasAuditLog;

    protected $connection = 'mongodb';
    protected $collection = 'profiles';

    protected $fillable = ['code', 'name', 'sections'];

    protected array $auditableFields = ['name', 'sections'];

    protected $casts = [
        'sections' => 'array',
    ];

    protected function getCodePrefix(): string
    {
        return 'PRF';
    }
}
