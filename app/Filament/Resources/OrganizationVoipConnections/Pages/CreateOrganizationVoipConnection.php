<?php

namespace App\Filament\Resources\OrganizationVoipConnections\Pages;

use App\Filament\Resources\OrganizationVoipConnections\OrganizationVoipConnectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganizationVoipConnection extends CreateRecord
{
    protected static string $resource = OrganizationVoipConnectionResource::class;

    public function mount(): void
    {
        parent::mount();

        $organizationId = request()->integer('organization_id');

        if ($organizationId) {
            $this->form->fill(['organization_id' => $organizationId]);
        }
    }
}
