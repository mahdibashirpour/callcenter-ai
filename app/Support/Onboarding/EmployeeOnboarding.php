<?php

namespace App\Support\Onboarding;

use App\Support\Navigation\EmployeeNavigation;
use Illuminate\Support\Facades\Route;

class EmployeeOnboarding
{
    /** @return array<string, string> */
    public static function routeUrls(): array
    {
        $urls = [];

        foreach (EmployeeNavigation::items() as $item) {
            if (! empty($item['route']) && Route::has($item['route'])) {
                $urls[$item['route']] = route($item['route']);
            }
        }

        return $urls;
    }
}
