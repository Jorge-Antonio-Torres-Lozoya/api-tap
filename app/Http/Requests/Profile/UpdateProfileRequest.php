<?php

namespace App\Http\Requests\Profile;

use App\Enums\SectionSlugEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $profileId = $this->route('profile')?->getKey();

        return [
            'name'       => ['sometimes', 'required', 'string', 'max:255', Rule::unique('profiles', 'name')->ignore($profileId, '_id')->whereNull('deleted_at')],
            'sections'   => ['sometimes', 'required', 'array', 'min:1'],
            'sections.*' => ['string', Rule::in(SectionSlugEnum::values())],
        ];
    }
}
