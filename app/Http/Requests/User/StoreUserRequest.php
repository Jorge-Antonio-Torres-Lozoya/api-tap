<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'username'           => ['required', 'email', 'unique:users,username'],
            'password'           => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'profile_photo'      => ['required', 'image', 'max:2048'],
            'phone'              => ['nullable', 'array'],
            'phone.country_code' => ['required_with:phone', 'string', 'regex:/^\+\d{1,4}$/'],
            'phone.number'       => ['required_with:phone', 'string', 'max:20'],
            'profile_ids'        => ['required', 'array', 'min:1'],
            'profile_ids.*'      => ['string', 'exists:mongodb.profiles,_id'],
        ];
    }
}
