<?php

namespace App\Models;

use App\Observers\AuditObserver;
use App\Traits\GeneratesCode;
use App\Traits\HasAuditLog;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use MongoDB\Laravel\Eloquent\Model;

#[ObservedBy([AuditObserver::class])]
class Product extends Model
{
    use HasSoftDelete, GeneratesCode, HasAuditLog;

    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = ['code', 'name', 'brand', 'price'];

    protected array $auditableFields = ['name', 'brand', 'price'];

    protected function getCodePrefix(): string
    {
        return 'PRD';
    }
}
