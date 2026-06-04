<?php

namespace App\Services;

use App\Mail\ResetPasswordMail;
use App\Models\PasswordReset;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(string $username, string $password): array
    {
        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'token' => $token,
            'user'  => [
                'id'       => (string) $user->getKey(),
                'code'     => $user->code,
                'name'     => $user->name,
                'username' => $user->username,
                'sections' => $user->getSectionSlugs(),
            ],
        ];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    public function forgotPassword(string $username): void
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => ['No existe una cuenta con ese correo electrónico.'],
            ]);
        }

        PasswordReset::where('username', $username)->delete();

        $token = hash('sha256', Str::random(60));

        PasswordReset::create([
            'username'   => $username,
            'token'      => $token,
            'expires_at' => now()->addMinutes(60),
        ]);

        Mail::to($username)->send(new ResetPasswordMail($token));
    }

    public function resetPassword(string $token, string $password): void
    {
        $reset = PasswordReset::where('token', $token)->first();

        if (!$reset || $reset->isExpired()) {
            throw ValidationException::withMessages([
                'token' => ['El token es inválido o ha expirado.'],
            ]);
        }

        $user = User::where('username', $reset->username)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'token' => ['El token es inválido o ha expirado.'],
            ]);
        }

        $user->update(['password' => $password]);

        $reset->delete();

        // Revoke all tokens so the user must log in again with the new password
        $user->tokens()->delete();
    }
}
