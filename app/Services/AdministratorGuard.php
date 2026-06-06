<?php

namespace App\Services;

use App\Enums\SectionSlugEnum;
use App\Exceptions\AdminLockoutException;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Guards the invariant that the system always keeps at least one administrator
 * (a user whose profiles together grant every section). Without it, deleting or
 * demoting the last admin — or stripping the profile that makes them one — would
 * lock everyone out of user and profile management.
 *
 * Each assertion simulates the requested change against the current world state
 * and throws when no administrator would remain.
 */
class AdministratorGuard
{
    public function assertUserDeletable(User $user): void
    {
        $users = $this->userProfileMap()->forget((string) $user->getKey());

        if (! $this->administratorExists($users, $this->profileSectionMap())) {
            throw new AdminLockoutException('No se puede eliminar al único administrador del sistema.');
        }
    }

    /** @param  array<int, string>  $newProfileIds */
    public function assertUserUpdatable(User $user, array $newProfileIds): void
    {
        $users = $this->userProfileMap()
            ->put((string) $user->getKey(), array_map('strval', $newProfileIds));

        if (! $this->administratorExists($users, $this->profileSectionMap())) {
            throw new AdminLockoutException('No se puede quitar el acceso de administrador al único administrador del sistema.');
        }
    }

    public function assertProfileDeletable(Profile $profile): void
    {
        $profiles = $this->profileSectionMap();
        unset($profiles[(string) $profile->getKey()]);

        if (! $this->administratorExists($this->userProfileMap(), $profiles)) {
            throw new AdminLockoutException('No se puede eliminar el perfil porque dejaría al sistema sin ningún administrador.');
        }
    }

    /** @param  array<int, string>  $newSections */
    public function assertProfileUpdatable(Profile $profile, array $newSections): void
    {
        $profiles = $this->profileSectionMap();
        $profiles[(string) $profile->getKey()] = array_map('strval', $newSections);

        if (! $this->administratorExists($this->userProfileMap(), $profiles)) {
            throw new AdminLockoutException('No se puede actualizar el perfil porque dejaría al sistema sin ningún administrador.');
        }
    }

    /**
     * @param  Collection<int|string, array<int, string>>  $users  each value is a list of profile ids
     * @param  array<string, array<int, string>>  $profileSections  profile id => section slugs
     */
    private function administratorExists(Collection $users, array $profileSections): bool
    {
        $required = SectionSlugEnum::values();

        return $users->contains(function (array $profileIds) use ($profileSections, $required) {
            $sections = [];

            foreach ($profileIds as $id) {
                $sections = array_merge($sections, $profileSections[$id] ?? []);
            }

            return empty(array_diff($required, array_unique($sections)));
        });
    }

    /** @return Collection<string, array<int, string>>  user id => profile ids */
    private function userProfileMap(): Collection
    {
        return User::get()->mapWithKeys(fn (User $user) => [
            (string) $user->getKey() => array_map('strval', $user->profile_ids ?? []),
        ]);
    }

    /** @return array<string, array<int, string>>  profile id => section slugs */
    private function profileSectionMap(): array
    {
        return Profile::get()
            ->mapWithKeys(fn (Profile $profile) => [
                (string) $profile->getKey() => array_map('strval', (array) $profile->sections),
            ])
            ->all();
    }
}
