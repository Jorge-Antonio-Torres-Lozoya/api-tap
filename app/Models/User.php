<?php

namespace App\Models;

use App\Observers\AuditObserver;
use App\Traits\GeneratesCode;
use App\Traits\HasAuditLog;
use App\Traits\HasSoftDelete;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Eloquent\Model;

#[ObservedBy([AuditObserver::class])]
class User extends Model implements AuthenticatableContract
{
    use Authenticatable, HasApiTokens, HasSoftDelete, GeneratesCode, HasAuditLog, Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'code',
        'name',
        'username',
        'password',
        'phone',
        'profile_photo',
        'profile_ids',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'phone'       => 'array',
        'profile_ids' => 'array',
        'password'    => 'hashed',
    ];

    protected array $auditableFields = ['name', 'username', 'phone', 'profile_photo', 'profile_ids'];

    protected array $excludedAuditFields = ['password', 'remember_token'];

    protected function getCodePrefix(): string
    {
        return 'USR';
    }

    // belongsToMany expects a pivot collection which doesn't exist in our MongoDB schema.
    // profile_ids is an array of ObjectIds embedded in the user document itself.
    public function profiles(): Collection
    {
        $ids = $this->profile_ids ?? [];

        if (empty($ids)) {
            return new Collection();
        }

        return Profile::whereIn('_id', $ids)->get();
    }

    public function getSectionSlugs(): array
    {
        return $this->profiles()
            ->pluck('sections')
            ->flatten()
            ->unique()
            ->values()
            ->toArray();
    }
}
