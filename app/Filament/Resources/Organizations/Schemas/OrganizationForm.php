<?php

namespace App\Filament\Resources\Organizations\Schemas;

use App\Enums\UserRole;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Toggle::make('disabled')
                    ->label(__('filament.fields.disabled')),
                Select::make('user_id')
                    ->label(__('filament.fields.employer'))
                    ->relationship(
                        name: 'employer',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('role', UserRole::Employer),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->native(false),
            ]);
    }
}
