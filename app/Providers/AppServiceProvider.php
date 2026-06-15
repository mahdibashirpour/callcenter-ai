<?php

namespace App\Providers;

use App\Filament\Support\FluentWidgetConfiguration;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(WidgetConfiguration::class, FluentWidgetConfiguration::class);
    }

    public function boot(): void
    {
        \Illuminate\Support\Carbon::macro('jalali', function (?string $format = null) {
            /** @var \Illuminate\Support\Carbon $this */
            return \App\Support\JalaliDate::format($this, $format ?? \App\Support\JalaliDate::DATE);
        });
    }
}
