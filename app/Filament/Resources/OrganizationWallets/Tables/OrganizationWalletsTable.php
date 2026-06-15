<?php

namespace App\Filament\Resources\OrganizationWallets\Tables;

use App\Models\PlatformAiSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Services\WalletService;

class OrganizationWalletsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('organization.title')
                    ->label(__('filament.fields.organization'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('balance')
                    ->money(fn ($record) => $record->currency ?? PlatformAiSettings::currencyCode())
                    ->sortable(),
                TextColumn::make('currency')->badge(),
                TextColumn::make('updated_at')->jalaliDateTime()->sortable(),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                Action::make('addCredits')
                    ->label(__('filament.actions.add_credits'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->persianNumeric(0)
                            ->required()
                            ->minValue(1)
                            ->step(1),
                        Textarea::make('description')
                            ->rows(2),
                    ])
                    ->action(function ($record, array $data) {
                        app(WalletService::class)->deposit(
                            $record->organization_id,
                            (float) $data['amount'],
                            $data['description'] ?? null,
                        );

                        Notification::make()
                            ->title(__('filament.notifications.credits_added'))
                            ->success()
                            ->send();
                    }),
                Action::make('deductCredits')
                    ->label(__('filament.actions.deduct_credits'))
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->form([
                        TextInput::make('amount')
                            ->persianNumeric(0)
                            ->required()
                            ->minValue(1)
                            ->step(1),
                        Textarea::make('description')
                            ->rows(2),
                    ])
                    ->action(function ($record, array $data) {
                        app(WalletService::class)->withdraw(
                            $record->organization_id,
                            (float) $data['amount'],
                            $data['description'] ?? null,
                        );

                        Notification::make()
                            ->title(__('filament.notifications.credits_deducted'))
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('organization.title');
    }
}
