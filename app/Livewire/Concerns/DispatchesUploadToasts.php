<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Log;

trait DispatchesUploadToasts
{
    protected function dispatchUploadToast(string $type, string $message, ?string $url = null): void
    {
        Log::info('Upload toast triggered', [
            'type' => $type,
            'message' => $message,
            'url' => $url,
            'component' => static::class,
            'user_id' => auth()->id(),
        ]);

        $detail = json_encode(
            ['type' => $type, 'message' => $message, 'url' => $url],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP,
        );

        $this->js("window.dispatchEvent(new CustomEvent('show-toast', { detail: {$detail} }))");
    }

    protected function dispatchUploadSuccessToast(?string $url = null): void
    {
        $this->dispatchUploadToast(
            type: 'success',
            message: 'فایل صوتی با موفقیت آپلود شد و به صف پردازش اضافه شد.',
            url: $url,
        );
    }

    protected function dispatchUploadErrorToast(string $message = 'آپلود فایل صوتی ناموفق بود. لطفاً دوباره تلاش کنید.'): void
    {
        $this->dispatchUploadToast(type: 'error', message: $message);
    }
}
