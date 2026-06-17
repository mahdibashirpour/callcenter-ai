<?php

namespace App\Support;

class UserFacingError
{
    public static function processing(?string $technicalMessage = null): string
    {
        if ($technicalMessage && config('app.debug')) {
            return $technicalMessage;
        }

        return __('ui.processing.error');
    }

    public static function upload(?string $technicalMessage = null): string
    {
        if ($technicalMessage && config('app.debug')) {
            return $technicalMessage;
        }

        return __('ui.processing.upload_error');
    }
}
