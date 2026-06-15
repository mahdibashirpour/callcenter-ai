<?php

namespace App\Filament\Pages\AiManagement;

use App\Filament\Concerns\OnlySuperAdmin;
use App\Models\AudioUploadSettings;
use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AudioUploadSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;
    use OnlySuperAdmin;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 6;

    protected static string $routePath = 'ai-management/upload-settings';

    protected string $view = 'filament.pages.ai-management.audio-upload-settings';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament.navigation.upload_settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.navigation.groups.ai_management');
    }

    public function getTitle(): string
    {
        return __('filament.pages.audio_upload_settings');
    }

    public function mount(): void
    {
        $settings = AudioUploadSettings::current();

        $this->form->fill([
            'max_file_size_mb' => round($settings->max_file_size_bytes / 1024 / 1024, 1),
            'max_duration_minutes' => (int) round($settings->max_duration_seconds / 60),
            'allowed_extensions' => $settings->allowed_extensions,
            'is_active' => $settings->is_active,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('is_active')
                    ->label(__('filament.fields.enable_manual_uploads')),
                TextInput::make('max_file_size_mb')
                    ->label(__('filament.fields.max_file_size_mb'))
                    ->persianNumeric(0)
                    ->required()
                    ->minValue(1)
                    ->maxValue(500),
                TextInput::make('max_duration_minutes')
                    ->label(__('filament.fields.max_duration_minutes'))
                    ->persianNumeric(0)
                    ->required()
                    ->minValue(1)
                    ->maxValue(240),
                TagsInput::make('allowed_extensions')
                    ->label(__('filament.fields.allowed_extensions'))
                    ->placeholder('mp3')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $settings = AudioUploadSettings::current();

        $settings->update([
            'is_active' => $data['is_active'],
            'max_file_size_bytes' => (int) ($data['max_file_size_mb'] * 1024 * 1024),
            'max_duration_seconds' => (int) ($data['max_duration_minutes'] * 60),
            'allowed_extensions' => array_map('strtolower', $data['allowed_extensions']),
        ]);

        Notification::make()
            ->title(__('filament.notifications.upload_settings_saved'))
            ->success()
            ->send();
    }
}
