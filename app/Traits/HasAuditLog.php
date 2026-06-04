<?php

namespace App\Traits;

trait HasAuditLog
{
    public function getAuditableFields(): array
    {
        return $this->auditableFields ?? [];
    }

    public function getExcludedAuditFields(): array
    {
        return $this->excludedAuditFields ?? ['password', 'remember_token'];
    }
}
