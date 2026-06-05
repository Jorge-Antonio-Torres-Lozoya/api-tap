<?php

namespace App\Services;

use App\Enums\AuditActionEnum;
use App\Mail\ResetPasswordMail;
use App\Models\AuditLog;
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
            AuditLog::recordAuth(AuditActionEnum::LOGIN_FAILED, null, ['username' => $username]);

            throw ValidationException::withMessages([
                'username' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Single active session per user: revoke previous tokens on each login.
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        AuditLog::recordAuth(AuditActionEnum::LOGIN, (string) $user->getKey());

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

        AuditLog::recordAuth(AuditActionEnum::LOGOUT, (string) $user->getKey());
    }

    public function forgotPassword(string $username): void
    {
        $user = User::where('username', $username)->first();

        // Do not reveal whether the account exists: return silently so the
        // controller always responds with the same generic success message.
        if (!$user) {
            return;
        }

        PasswordReset::where('username', $username)->delete();

        // Email the plain token to the user but only persist its hash, so a
        // database read cannot be used to hijack pending reset requests.
        $plainToken = Str::random(64);

        PasswordReset::create([
            'username'   => $username,
            'token'      => hash('sha256', $plainToken),
            'expires_at' => now()->addMinutes(60),
        ]);

        Mail::to($username)->send(new ResetPasswordMail($plainToken));
    }

    public function resetPassword(string $token, string $password): void
    {
        $reset = PasswordReset::where('token', hash('sha256', $token))->first();

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

        AuditLog::recordAuth(AuditActionEnum::PASSWORD_RESET, (string) $user->getKey());
    }
}
