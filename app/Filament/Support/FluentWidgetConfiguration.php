<?php

namespace App\Filament\Support;

use Filament\Widgets\WidgetConfiguration;

class FluentWidgetConfiguration extends WidgetConfiguration
{
    public function heading(?string $heading): static
    {
        if ($heading !== null) {
            $this->properties['chartHeading'] = $heading;
        }

        return $this;
    }
}
