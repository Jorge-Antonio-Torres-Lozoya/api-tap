<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->getKey();

        return [
            'name'               => ['sometimes', 'required', 'string', 'max:255'],
            'username'           => ['sometimes', 'required', 'email', Rule::unique('users', 'username')->ignore($userId, '_id')->whereNull('deleted_at')],
            'password'           => ['sometimes', 'required', 'confirmed', Password::min(8)->letters()->numbers()],
            'profile_photo'      => ['sometimes', 'image', 'max:2048'],
            'phone'              => ['nullable', 'array'],
            'phone.country_code' => ['required_with:phone', 'string', 'regex:/^\+\d{1,4}$/'],
            'phone.number'       => ['required_with:phone', 'string', 'max:20'],
            'profile_ids'        => ['sometimes', 'required', 'array', 'min:1'],
            'profile_ids.*'      => ['string', 'exists:mongodb.profiles,_id'],
        ];
    }
}
