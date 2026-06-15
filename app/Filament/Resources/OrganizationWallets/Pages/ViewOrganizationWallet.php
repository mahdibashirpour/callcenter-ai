<?php

namespace App\Filament\Resources\OrganizationWallets\Pages;

use App\Filament\Resources\OrganizationWallets\OrganizationWalletResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewOrganizationWallet extends ViewRecord
{
    protected static string $resource = OrganizationWalletResource::class;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getRelationManagersContentComponent(),
            ]);
    }
}
