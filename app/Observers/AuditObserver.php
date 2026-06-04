<?php

namespace App\Observers;

use App\Enums\AuditActionEnum;
use App\Models\AuditLog;
use App\Traits\HasAuditLog;
use Illuminate\Support\Facades\Auth;
use MongoDB\Laravel\Eloquent\Model;

class AuditObserver
{
    public function created(Model $model): void
    {
        AuditLog::create([
            'collection'    => $model->getCollection(),
            'document_id'   => (string) $model->getKey(),
            'action'        => AuditActionEnum::CREATE,
            'previous_data' => null,
            'current_data'  => $this->extractFields($model, $model->getAttributes()),
            'performed_by'  => Auth::id(),
        ]);
    }

    public function updating(Model $model): void
    {
        $dirty = $this->extractFields($model, $model->getDirty());

        if (empty($dirty)) {
            return;
        }

        AuditLog::create([
            'collection'    => $model->getCollection(),
            'document_id'   => (string) $model->getKey(),
            'action'        => AuditActionEnum::UPDATE,
            'previous_data' => $this->extractFields($model, $model->getOriginal()),
            'current_data'  => $dirty,
            'performed_by'  => Auth::id(),
        ]);
    }

    public function deleting(Model $model): void
    {
        AuditLog::create([
            'collection'    => $model->getCollection(),
            'document_id'   => (string) $model->getKey(),
            'action'        => AuditActionEnum::DELETE,
            'previous_data' => $this->extractFields($model, $model->getAttributes()),
            'current_data'  => null,
            'performed_by'  => Auth::id(),
        ]);
    }

    private function extractFields(Model $model, array $data): array
    {
        if (!in_array(HasAuditLog::class, class_uses_recursive($model))) {
            return $data;
        }

        $auditable = $model->getAuditableFields();
        $excluded  = $model->getExcludedAuditFields();

        if (empty($auditable)) {
            return array_diff_key($data, array_flip($excluded));
        }

        return array_intersect_key($data, array_flip($auditable));
    }
}
