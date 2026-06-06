<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Prevents privilege escalation through profiles: a user may only assign
 * sections they already have access to. Without this, a user who manages
 * profiles could grant themselves (or others) any section by creating or
 * editing a profile with sections they do not own.
 */
class CanOnlyAssignOwnedSections implements ValidationRule
{
    /** @param  array<int, string>  $ownedSections */
    public function __construct(private readonly array $ownedSections) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $requested = is_array($value) ? $value : [];
        $notOwned = array_values(array_diff($requested, $this->ownedSections));

        if (! empty($notOwned)) {
            $fail('Solo puedes asignar secciones a las que tú tienes acceso. No puedes otorgar: '.implode(', ', $notOwned).'.');
        }
    }
}
