<?php

namespace App\Models\Concerns;

use App\Support\AvatarPresenter;

trait HasAvatar
{
    public function avatarUrl(): ?string
    {
        $path = $this->avatar_path ?? null;

        if (filled($path)) {
            return AvatarPresenter::publicUrl($path);
        }

        if (method_exists($this, 'user')) {
            $userPath = $this->user?->avatar_path ?? null;

            if (filled($userPath)) {
                return AvatarPresenter::publicUrl($userPath);
            }
        }

        return null;
    }

    public function avatarName(): string
    {
        if (property_exists($this, 'first_name') || isset($this->first_name)) {
            return $this->full_name;
        }

        return (string) ($this->name ?? '?');
    }
}
