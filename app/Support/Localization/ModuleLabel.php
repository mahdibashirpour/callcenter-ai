<?php

namespace App\Support\Localization;

class ModuleLabel
{
    public static function for(string $key): string
    {
        return __("modules.{$key}", [], 'fa') !== "modules.{$key}"
            ? __("modules.{$key}", [], 'fa')
            : $key;
    }
}
