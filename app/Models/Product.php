<?php

namespace App\Models;

use App\Traits\GeneratesCode;
use App\Traits\HasAuditLog;
use App\Traits\HasSoftDelete;
use MongoDB\Laravel\Eloquent\Model;

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
