<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class RecordingStorage
{
    public function disk(): string
    {
        return (string) config('recordings.disk', 'local');
    }

    public function put(string $path, string $contents, ?string $mimeType = null): void
    {
        $options = [];

        if ($mimeType) {
            $options['ContentType'] = $mimeType;
            $options['mimetype'] = $mimeType;
        }

        Storage::disk($this->disk())->put($path, $contents, $options);
    }

    public function exists(string $path): bool
    {
        return Storage::disk($this->disk())->exists($path);
    }

    public function get(string $path): string
    {
        return Storage::disk($this->disk())->get($path);
    }
}
