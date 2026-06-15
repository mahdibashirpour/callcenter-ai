<?php

namespace App\Support;

class UserFacingError
{
    public static function processing(?string $technicalMessage = null): string
    {
        if ($technicalMessage && config('app.debug')) {
            return $technicalMessage;
        }

        return 'خطا در پردازش فایل. لطفاً دوباره تلاش کنید یا با پشتیبانی تماس بگیرید.';
    }

    public static function upload(?string $technicalMessage = null): string
    {
        if ($technicalMessage && config('app.debug')) {
            return $technicalMessage;
        }

        return 'خطا در پردازش فایل.';
    }
}
