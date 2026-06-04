<?php

namespace App\Http\Requests\Profile;

use App\Enums\SectionSlugEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255', Rule::unique('profiles', 'name')],
            'sections'   => ['required', 'array', 'min:1'],
            'sections.*' => ['string', Rule::in(SectionSlugEnum::values())],
        ];
    }
}
