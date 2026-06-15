<?php

namespace App\Support\Seeding;

use App\Models\Organization;
use App\Models\User;

final class DemoAvatarAssigner
{
    /** @var list<string> */
    private array $used = [];

    /** @param  list<string>  $paths */
    public function seedUsed(array $paths): void
    {
        foreach ($paths as $path) {
            if (filled($path) && ! in_array($path, $this->used, true)) {
                $this->used[] = $path;
            }
        }
    }

    public static function forOrganization(Organization $organization): self
    {
        $assigner = new self;

        $userIds = $organization->memberships()
            ->pluck('user_id')
            ->when($organization->user_id, fn ($ids) => $ids->push($organization->user_id))
            ->unique()
            ->values();

        if ($userIds->isNotEmpty()) {
            $assigner->seedUsed(
                User::query()->whereIn('id', $userIds)->pluck('avatar_path')->filter()->all(),
            );
        }

        return $assigner;
    }

    public function assign(string $gender): string
    {
        $pool = $gender === 'female' ? DemoCatalog::FEMALE_AVATARS : DemoCatalog::MALE_AVATARS;

        foreach ($pool as $avatar) {
            if (! in_array($avatar, $this->used, true)) {
                $this->used[] = $avatar;

                return $avatar;
            }
        }

        $genderSlotsUsed = count(array_intersect($this->used, $pool));
        $avatar = $pool[$genderSlotsUsed % count($pool)];
        $this->used[] = $avatar;

        return $avatar;
    }

    /** @return list<string> */
    public function usedPaths(): array
    {
        return $this->used;
    }
}
